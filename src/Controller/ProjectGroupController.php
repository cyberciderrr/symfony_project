<?php
namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroupType;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/project-groups')]
class ProjectGroupController extends AbstractController
{
    #[Route('/', name: 'project_group_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $repository): JsonResponse
    {
        $projectGroups = $repository->findAll();
        return $this->json(['data' => $projectGroups]);
    }

    #[Route('/{id}', name: 'project_group_show', methods: ['GET'])]
    public function show(ProjectGroup $projectGroup): JsonResponse
    {
        return $this->json(['data' => $projectGroup]);
    }

    #[Route('/', name: 'project_group_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $projectGroup = new ProjectGroup();
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->submit(json_decode($request->getContent(), true));

        if ($form->isValid()) {
            $em->persist($projectGroup);
            $em->flush();
            return $this->json(['data' => $projectGroup], 201);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'project_group_update', methods: ['PATCH'])]
    public function update(ProjectGroup $projectGroup, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(ProjectGroupType::class, $projectGroup);
        $form->submit(json_decode($request->getContent(), true), false);

        if ($form->isValid()) {
            $em->flush();
            return $this->json(['data' => $projectGroup]);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $this->json(['data' => $errors], 400);
    }

    #[Route('/{id}', name: 'project_group_delete', methods: ['DELETE'])]
    public function delete(ProjectGroup $projectGroup, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($projectGroup);
        $em->flush();
        return $this->json([], 204);
    }
}
