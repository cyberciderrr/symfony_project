<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ProjectGroup;

#[Route('/api/projects')]
class ProjectController extends AbstractController
{
    private SymfonySerializerInterface $serializer;

    public function __construct(SymfonySerializerInterface $serializer, private EntityManagerInterface $entityManager, private ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        $data = json_decode($request->getContent(), true);
        $projectGroup = $this->entityManager->getRepository(ProjectGroup::class)->find($data['group_id']);
        if ($projectGroup) {
            $project->setProjectGroup($projectGroup);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($project);
            $this->entityManager->flush();
            return $this->json(['data' => $project], Response::HTTP_CREATED);
        }
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $field = $error->getOrigin()->getName();
            if (!isset($errors[$field])) {
                $errors[$field] = [];
            }
            $errors[$field][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();
        $data = [];
        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'group' => $project->getProjectGroup() ? [
                    'id' => $project->getProjectGroup()->getId(),
                    'name' => $project->getProjectGroup()->getName(),
                ] : null,
                'tasks' => array_map(function ($task) {
                    return [
                        'id' => $task->getId(),
                        'name' => $task->getName(),
                        'description' => $task->getDescription(),
                    ];
                }, $project->getTasks()->toArray())
            ];
        }
        return $this->json(['data' => $data]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }
        $tasks = $project->getTasks()->toArray();

        $data = [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'group' => $project->getProjectGroup() ? [
                'id' => $project->getProjectGroup()->getId(),
                'name' => $project->getProjectGroup()->getName(),
            ] : null,
            'tasks' => array_map(function ($task) {
                return [
                    'id' => $task->getId(),
                    'name' => $task->getName(),
                    'description' => $task->getDescription(),
                ];
            }, $tasks)
        ];
        return $this->json(['data' => $data]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->json(['data' => $project]);
        }
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $field = $error->getOrigin()->getName();
            if (!isset($errors[$field])) {
                $errors[$field] = [];
            }
            $errors[$field][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['message' => 'Project not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($project);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}