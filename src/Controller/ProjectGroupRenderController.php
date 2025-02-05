<?php

namespace App\Controller;

use App\Entity\ProjectGroup;
use App\Form\ProjectGroup1Type;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project/group/render')]
final class ProjectGroupRenderController extends AbstractController
{
    #[Route(name: 'app_project_group_render_index', methods: ['GET'])]
    public function index(ProjectGroupRepository $projectGroupRepository): Response
    {
        return $this->render('project_group_render/index.html.twig', [
            'project_groups' => $projectGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_group_render_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projectGroup = new ProjectGroup();
        $form = $this->createForm(ProjectGroup1Type::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_group_render_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_group_render/new.html.twig', [
            'project_group' => $projectGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_group_render_show', methods: ['GET'])]
    public function show(ProjectGroup $projectGroup): Response
    {
        return $this->render('project_group_render/show.html.twig', [
            'project_group' => $projectGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_group_render_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectGroup1Type::class, $projectGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_group_render_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_group_render/edit.html.twig', [
            'project_group' => $projectGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_group_render_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectGroup $projectGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projectGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_group_render_index', [], Response::HTTP_SEE_OTHER);
    }
}
