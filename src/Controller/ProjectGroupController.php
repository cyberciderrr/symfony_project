<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/project_groups')]
class ProjectGroupController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $projectGroup = new ProjectGroup();
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($projectGroup);
            $this->entityManager->flush();
            return $this->json(['data' => $projectGroup], Response::HTTP_CREATED);
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
        $projectGroups = $this->entityManager->getRepository(ProjectGroup::class)->findAll();
        $data = [];
        foreach ($projectGroups as $group) {
            $data[] = [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'projects' => array_map(function ($project) {
                    return [
                        'id' => $project->getId(),
                        'name' => $project->getName(),
                        'tasks' => array_map(function ($task) {
                            return [
                                'id' => $task->getId(),
                                'name' => $task->getName(),
                                'description' => $task->getDescription(),
                            ];
                        }, $project->getTasks()->toArray())
                    ];
                }, $group->getProjects()->toArray())
            ];
        }
        return $this->json(['data' => $data]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $projectGroup = $this->entityManager->getRepository(ProjectGroup::class)->find($id);

        if (!$projectGroup) {
            return $this->json(['message' => 'Project group not found'], Response::HTTP_NOT_FOUND);
        }

        $projects = $projectGroup->getProjects()->toArray();

        $data = [
            'id' => $projectGroup->getId(),
            'name' => $projectGroup->getName(),
            'projects' => array_map(function ($project) {
                return [
                    'id' => $project->getId(),
                    'name' => $project->getName(),
                    'tasks' => array_map(function ($task) {
                        return [
                            'id' => $task->getId(),
                            'name' => $task->getName(),
                            'description' => $task->getDescription(),
                        ];
                    }, $project->getTasks()->toArray())
                ];
            }, $projects)
        ];
        return $this->json(['data' => $data]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $projectGroup = $this->entityManager->getRepository(ProjectGroup::class)->find($id);

        if (!$projectGroup) {
            return $this->json(['message' => 'Project group not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->json(['data' => $projectGroup]);
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
        $projectGroup = $this->entityManager->getRepository(ProjectGroup::class)->find($id);

        if (!$projectGroup) {
            return $this->json(['message' => 'Project group not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($projectGroup);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}