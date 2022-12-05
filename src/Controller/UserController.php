<?php

namespace App\Controller;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    #[Route('/recup/user', name: 'recup_user')]
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

    #[Route('/valid/user', name: 'valid_user')]
    public function validUser(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): JsonResponse
    {
        $user= $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }


        if ($user->getRoles() === ['ROLE_ADMIN']){
            $userValid = $userRepository->findOneBy(['email'=>$data['email']]);

            $userValid-> setIsActive(true);

            $mydate = getDate(strtotime($data['dateValid']));
            $dateValid = new \DateTime();
            date_date_set($dateValid, $mydate['year'], $mydate['mon'], $mydate['mday']);
    
            $userValid->setActiveAt($dateValid);

            $entityManager->persist($userValid);
            $entityManager->flush();
 
            return $this->json([
                'message' => 'Profil validé',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'message'=>'non'
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[Route('/listuser/admin', name: 'listUser')]
    public function recupListUser(): JsonResponse
    {
        $user= $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'message'=>$user
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
