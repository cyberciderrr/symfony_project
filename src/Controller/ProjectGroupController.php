<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProjectGroupController extends AbstractController
{

    private $serializer;
    public function __construct(SerializerInterface $serializer, private EntityManagerInterface $entityManager, private ValidatorInterface $validator)
    {
        $this->serializer = $serializer;
    }




    #[Route('', methods: ['POST'])]
        public function create(Request $request): JsonResponse
    {
        $projectGroup = new ProjectGroup();
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
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
            return $this->json(['data' => $projectGroups]);
        }

       #[Route('/{id}', methods: ['GET'])]
        public function get(int $id): JsonResponse
    {
        $projectGroup = $this->entityManager->getRepository(ProjectGroup::class)->find($id);

        if (!$projectGroup) {
            return $this->json(['message' => 'Project group not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $projectGroup]);
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
        if($form->isSubmitted() && $form->isValid()){
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
