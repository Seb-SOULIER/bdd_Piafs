<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    #[Route('/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $user=$this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'app_profil', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $user  =$this->getUser();
        return $this->render('profil/index.html.twig', [
            'user' => $userRepository->find($user),
        ]);
    }

    // #[Route('/new', name: 'app_profil_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, UserRepository $userRepository): Response
    // {
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userRepository->save($user, true);

    //         return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('profil/new.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_profil_show', methods: ['GET'])]
    // public function show(User $user): Response
    // {
    //     return $this->render('profil/show.html.twig', [
    //         'user' => $user,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, User $user, UserRepository $userRepository): Response
    // {
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $userRepository->save($user, true);

    //         return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('profil/edit.html.twig', [
    //         'user' => $user,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_profil_delete', methods: ['POST'])]
    // public function delete(Request $request, User $user, UserRepository $userRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
    //         $userRepository->remove($user, true);
    //     }

    //     return $this->redirectToRoute('app_profil_index', [], Response::HTTP_SEE_OTHER);
    // }
}
