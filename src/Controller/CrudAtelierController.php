<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/crud/atelier')]
class CrudAtelierController extends AbstractController
{
    #[Route('/', name: 'app_crud_atelier_index', methods: ['GET'])]
    public function index(AtelierRepository $atelierRepository): Response
    {
        return $this->render('crud_atelier/index.html.twig', [
            'ateliers' => $atelierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_crud_atelier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AtelierRepository $atelierRepository): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $atelierRepository->save($atelier, true);

            return $this->redirectToRoute('app_crud_atelier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crud_atelier/new.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_atelier_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {
        return $this->render('crud_atelier/show.html.twig', [
            'atelier' => $atelier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crud_atelier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, AtelierRepository $atelierRepository): Response
    {
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $atelierRepository->save($atelier, true);

            return $this->redirectToRoute('app_crud_atelier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crud_atelier/edit.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crud_atelier_delete', methods: ['POST'])]
    public function delete(Request $request, Atelier $atelier, AtelierRepository $atelierRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$atelier->getId(), $request->request->get('_token'))) {
            $atelierRepository->remove($atelier, true);
        }

        return $this->redirectToRoute('app_crud_atelier_index', [], Response::HTTP_SEE_OTHER);
    }
}
