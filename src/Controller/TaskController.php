<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        $data = json_decode($request->getContent(), true);
        $task->setProject($this->entityManager->getRepository(Project::class)->find($data['project_id']));

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            return $this->json(['data' => $task], Response::HTTP_CREATED);
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
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        return $this->json(['data' => $tasks]);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $task]);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->json(['data' => $task]);
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
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->json(['message' => 'Task not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}