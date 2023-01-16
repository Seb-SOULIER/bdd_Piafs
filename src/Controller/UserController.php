<?php

namespace App\Controller;

use App\Entity\Children;
use App\Repository\ChildrenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/recup/user', name: 'recup_user')]
    
    public function recupUser(): JsonResponse
    {
        $user = $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $profil=false;

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

        $childrenArray=[];
        $childrens = $user->getChildrens();
        foreach($childrens as $children){
            array_push($childrenArray,[
                'name'=>$children->getName(),
                'firstname'=>$children->getFirstname(),
                'birthdate'=>$children->getBirthdate(),
                'isActive'=>$children->isIsActive(),
                'activeAt'=>$children->getActiveAt()
            ]);
        }


        $userSend = [
            'id'=>$user->getId(),
            'email'=> $user->getEmail(),
            'userIdentifier'=>$user->getEmail(),
            'username'=>$user->getEmail(),
            'roles'=> $user->getRoles(),
            'lastname'=> $user->getLastname(),
            'firstname'=> $user->getFirstname(),
            'birthdate'=> $user->getBirthdate(),
            'avatar'=> $user->getAvatar(),
            'address'=> $user->getAddress(),
            'zipcode'=>$user->getZipcode(),
            'city'=>$user->getCity(),
            'phone'=>$user->getPhone(),
            'subcribeAt'=> $user->getSubcribeAt(),
            'children'=>$childrenArray
        ];

        return $this->json([
            'user'  => $userSend,
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
    public function recupListUser(Request $request, UserRepository $userRepository, ChildrenRepository $childrenRepository): JsonResponse
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

            $listUserChildren = [];
            
            if($user->getRoles() === ['ROLE_ADMIN']){
                
                if($select2 === 'Actifs'){
                    $childrens = $childrenRepository->findBy(['isActive'=>true]);
                    foreach($childrens as $children){
                        if ($select === 'Administrateurs'){
                            if($children->getParent()->getRoles() === ["ROLE_ADMIN"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }elseif($select === 'Intervenants'){
                            if($children->getParent()->getRoles() === ["ROLE_INTER"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }elseif($select === 'Utilisateurs'){
                            if($children->getParent()->getRoles() === ["ROLE_USER"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }else{
                            array_push($listUserChildren,[$children]);
                        }
                    }
                }elseif($select2 === 'Inactifs'){
                    $childrens = $childrenRepository->findBy(['isActive'=>false]);
                    foreach($childrens as $children){
                        if ($select === 'Administrateurs'){
                            if($children->getParent()->getRoles() === ["ROLE_ADMIN"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }elseif($select === 'Intervenants'){
                            if($children->getParent()->getRoles() === ["ROLE_INTER"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }elseif($select === 'Utilisateurs'){
                            if($children->getParent()->getRoles() === ["ROLE_USER"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }else{
                            array_push($listUserChildren,[$children]);
                        }
                    }
                }else{
                    $childrens = $childrenRepository->findAll();
                    foreach($childrens as $children){
                        if ($select === 'Administrateurs'){
                            if($children->getParent()->getRoles() === ["ROLE_ADMIN"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }elseif($select === 'Intervenants'){
                            if($children->getParent()->getRoles() === ["ROLE_INTER"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }elseif($select === 'Utilisateurs'){
                            if($children->getParent()->getRoles() === ["ROLE_USER"] ){
                                array_push($listUserChildren,[$children]);
                            }
                        }else{
                            array_push($listUserChildren,[$children]);
                        }
                    }
                }

                $listUserSend = [];
                foreach($listUserChildren as $childrenOne){    
                    array_push($listUserSend,[
                        'id'=>$childrenOne[0]->getId(),
                        'name'=> $childrenOne[0]->getName(),
                        'firstname'=>$childrenOne[0]->getFirstname(),
                        'isActive'=>$childrenOne[0]->isIsActive(),
                        'activeAt'=> $childrenOne[0]->getActiveAt(),
                        'parent'=> $childrenOne[0]->getParent()->getEmail(),
                        'firstnameParent'=> $childrenOne[0]->getParent()->getFirstname(),
                        'lastnameParent'=> $childrenOne[0]->getParent()->getLastname(),
                        'roleParent'=>$childrenOne[0]->getParent()->getRoles()
                    ]);
                }

                return $this->json($listUserSend);
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

        if($data['birthdate'] == "15"){
            $birthdate = new \DateTime();
        }else{
            $mydate = getDate(strtotime($data['birthdate']));
            $birthdate = new \DateTime();
            date_date_set($birthdate, $mydate['year'], $mydate['mon'], $mydate['mday']);
        }
        
        $children->setBirthdate($birthdate);
        $children->setParent($user);

        $entityManager->persist($children);
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

        $childrenArray=[];
        $childrens = $user->getChildrens();
        foreach($childrens as $children){
            array_push($childrenArray,['name'=>$children->getName(),'birthdate'=>$children->getBirthdate()]);
        }


        $userSend = [
            'id'=>$user->getId(),
            'email'=> $user->getEmail(),
            'userIdentifier'=>$user->getEmail(),
            'username'=>$user->getEmail(),
            'roles'=> $user->getRoles(),
            'lastname'=> $user->getLastname(),
            'firstname'=> $user->getFirstname(),
            'birthdate'=> $user->getBirthdate(),
            'avatar'=> $user->getAvatar(),
            'address'=> $user->getAddress(),
            'zipcode'=>$user->getZipcode(),
            'city'=>$user->getCity(),
            'phone'=>$user->getPhone(),
            'subcribeAt'=> $user->getSubcribeAt(),
            'isActive'=> $user->isIsActive(),
            'activeAt'=> $user->getActiveAt(),
            'children'=>$childrenArray
        ];

        return $this->json([
            'user'  => $userSend,
            'profil' => $profil
        ]);
    }
}
