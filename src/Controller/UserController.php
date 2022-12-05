<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    #[Route('/recup/user', name: 'api_user')]
    public function recupUser(): JsonResponse
    {
        $user= $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($user->getValidToken() < new DateTime()){
            return $this->json([
                'message' => 'Merci de vous reconnecter',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user'  => $user,
        ]);
    }
}
