<?php

namespace App\Controller;

use App\Entity\Children;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ActiveUser;
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
    public function recupUser(Request $request, UserRepository $userRepository): JsonResponse
    {
     
        $data = json_decode($request->getContent(), true);
     
        $user = new User;
        $user = $userRepository->findOneBy(['email'=>$data['email']]);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

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
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[Route('/valid/user', name: 'valid_user')]
    public function validUser(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): JsonResponse
    {
        $user= $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
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
                'message' => 'success',
            ]);
        }

        return $this->json([
            'message'=>'non'
        ]);
    }

    #[Route('/listuser/admin', name: 'listUser')]
    public function recupListUser(Request $request, UserRepository $userRepository): JsonResponse
    {
        $user= $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $data = json_decode($request->getContent(), true);
        if ($data){
            $select = $data['select'];
            $select2 = $data['select2'];

            if($user->getRoles() === ['ROLE_ADMIN']){
                if ($select === 'Administrateurs'){
                    if($select2 === 'Actifs'){
                        $listUser = $userRepository->findAllUser('["ROLE_ADMIN"]','true');
                    }elseif($select2 === 'Inactifs'){
                        $listUser = $userRepository->findAllUser('["ROLE_ADMIN"]','false');
                    }else{
                        $listUser = $userRepository->findAllUser('["ROLE_ADMIN"]',null);
                    }
                }elseif($select === 'Intervenants'){
                    if($select2 === 'Actifs'){
                        $listUser = $userRepository->findAllUser('["ROLE_INTER"]','true');
                    }elseif($select2 === 'Inactifs'){
                        $listUser = $userRepository->findAllUser('["ROLE_INTER"]','false');
                    }else{
                        $listUser = $userRepository->findAllUser('["ROLE_INTER"]',null);
                    }
                }elseif($select === 'Utilisateurs'){
                    if($select2 === 'Actifs'){
                        $listUser = $userRepository->findAllUser('["ROLE_USER"]','true');
                    }elseif($select2 === 'Inactifs'){
                        $listUser = $userRepository->findAllUser('["ROLE_USER"]','false');
                    }else{
                        $listUser = $userRepository->findAllUser('["ROLE_USER"]',null);
                    }
                }else{
                    if($select2 === 'Actifs'){
                        $listUser = $userRepository->findAllUser(null,'true');
                    }elseif($select2 === 'Inactifs'){
                        $listUser = $userRepository->findAllUser(null,'false');
                    }else{
                        $listUser = $userRepository->findAllUser(null,null);
                    }
                }
                return $this->json($listUser);
            }
        }

        return $this->json([
            'message'=>'erreur'
        ]);
        // , JsonResponse::HTTP_UNAUTHORIZED);
    }


    #[Route('/role/user', name: 'role_user')]
    public function roleUser(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): JsonResponse
    {
        $user= $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }


        if ($user->getRoles() === ['ROLE_ADMIN']){
            $userValid = $userRepository->findOneBy(['email'=>$data['email']]);

            $userValid->setRoles([$data['role']]);

            $entityManager->persist($userValid);
            $entityManager->flush();
 
            return $this->json([
                'message' => 'success',
            ]);
        }

        return $this->json([
            'message'=>'non'
        ]);
    }

    #[Route('/addChildren/user', name: 'children_user')]
    public function addChildrenUser(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): JsonResponse
    {
        $user= $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $children = new Children();
        $children->setName($data['name']);

        if($data['birthdate'] === "15"){
            $birthdate = new Date();
        }else{
            $mydate = getDate(strtotime($data['birthdate']));
            $birthdate = new \DateTime();
            date_date_set($birthdate, $mydate['year'], $mydate['mon'], $mydate['mday']);
        }
        
        $children->setBirthdate($birthdate);
        $children->setParent($user);

        $entityManager->persist($children);
        $entityManager->flush();

        return $this->json([
            'message' => 'Success',
        ]);
    }
}
