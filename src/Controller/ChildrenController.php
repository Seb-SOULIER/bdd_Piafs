<?php

namespace App\Controller;

use App\Entity\Children;
use App\Form\ChildrenType;
use App\Repository\ChildrenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/children')]
class ChildrenController extends AbstractController
{
    // #[Route('/', name: 'app_children_index', methods: ['GET'])]
    // public function index(ChildrenRepository $childrenRepository): Response
    // {
    //     return $this->render('children/index.html.twig', [
    //         'childrens' => $childrenRepository->findAll(),
    //     ]);
    // }

    #[Route('/new', name: 'app_children_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ChildrenRepository $childrenRepository): Response
    {
        $user=$this->getUser();
        
        $child = new Children();
        $form = $this->createForm(ChildrenType::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $child->setParent($user);
            $child->setIsActive(false);
            $childrenRepository->save($child, true);

            return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('children/new.html.twig', [
            'child' => $child,
            'form' => $form,
            'user'=>$user
        ]);
    }

    // #[Route('/{id}', name: 'app_children_show', methods: ['GET'])]
    // public function show(Children $child): Response
    // {
    //     return $this->render('children/show.html.twig', [
    //         'child' => $child,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_children_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Children $child, ChildrenRepository $childrenRepository): Response
    // {
    //     $form = $this->createForm(ChildrenType::class, $child);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $childrenRepository->save($child, true);

    //         return $this->redirectToRoute('app_children_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('children/edit.html.twig', [
    //         'child' => $child,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_children_delete', methods: ['POST'])]
    // public function delete(Request $request, Children $child, ChildrenRepository $childrenRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$child->getId(), $request->request->get('_token'))) {
    //         $childrenRepository->remove($child, true);
    //     }

    //     return $this->redirectToRoute('app_children_index', [], Response::HTTP_SEE_OTHER);
    // }
}
