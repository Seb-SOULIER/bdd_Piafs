<?php

namespace App\Controller;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    #[Route('/recup/user', name: 'api_user')]
    public function recupUser(EntityManagerInterface $entityManager): JsonResponse
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

        if($user->getActiveAt()){
            if($user->getActiveAt() > new Date()){
                $user->setIsActive(true);
            }else{
                $user->setIsActive(false);
            }
        }else{
            $user->setIsActive(false);
        }
        $entityManager->persist($user);
        $entityManager->flush();

        $profil=false;

        if ($user->isIsActive() === false) {
            if (
                $user->getLastname() and 
                $user->getFirstname() and
                $user->getBirthdate() and
                $user->getAddress() and
                $user->getZipcode() and
                $user->getCity() and
                $user->getPhone()
            ){
                $profil = true;
            }
        }

        return $this->json([
            'user'  => $user,
            'profil' => $profil
        ]);
    }
}
