<?php

namespace App\Controller;

use App\Repository\ChildrenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/app/user/recup', name: 'recup_user')]
    
    public function recupUser(UserRepository $userRepository): JsonResponse
    {
        $userConnect = $this->getUser();
        if (null === $userConnect) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        $profil = false;

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

    #[Route('/app/admin/user/select', name: 'recup_Adminuser')]
    
    public function recupAdminUser(UserRepository $userRepository, Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $profil=false;

        $data = json_decode($request->getContent(), true);
        $userOne = $userRepository->findOneBy(['id'=>$data['idUser']]);

        if (
            $userOne->getLastname() and 
            $userOne->getFirstname() and
            $userOne->getBirthdate() and
            $userOne->getAddress() and
            $userOne->getZipcode() and
            $userOne->getCity() and
            $userOne->getPhone()
        ){
            $profil = true;
        }

        $childrenArray=[];
        $childrens = $userOne->getChildrens();
        foreach($childrens as $children){
            array_push($childrenArray,[
                'id'=>$children->getId(),
                'name'=>$children->getName(),
                'firstname'=>$children->getFirstname(),
                'birthdate'=>$children->getBirthdate(),
                'isActive'=>$children->isIsActive(),
                'activeAt'=>$children->getActiveAt()
            ]);
        }


        $userSend = [
            'id'=>$userOne->getId(),
            'email'=> $userOne->getEmail(),
            'userIdentifier'=>$userOne->getEmail(),
            'username'=>$userOne->getEmail(),
            'roles'=> $userOne->getRoles(),
            'lastname'=> $userOne->getLastname(),
            'firstname'=> $userOne->getFirstname(),
            'birthdate'=> $userOne->getBirthdate(),
            'avatar'=> $userOne->getAvatar(),
            'address'=> $userOne->getAddress(),
            'zipcode'=>$userOne->getZipcode(),
            'city'=>$userOne->getCity(),
            'phone'=>$userOne->getPhone(),
            'subcribeAt'=> $userOne->getSubcribeAt(),
            'children'=>$childrenArray
        ];

        return $this->json([
            'user'  => $userSend,
            'profil' => $profil
        ]);
    }

    // #[Route('/valid/user', name: 'valid_user')]
    // public function validUser(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): JsonResponse
    // {
    //     $userConnect = $this->getUser();
    //     $data = json_decode($request->getContent(), true);

    //     if (null === $userConnect) {
    //         return $this->json([
    //             'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
    //         ]);
    //     }

    //     $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

    //     if ($user->getRoles() === ['ROLE_ADMIN']){
    //         $userValid = $userRepository->findOneBy(['email'=>$data['email']]);

    //         $userValid->setIsActive(true);

    //         $mydate = getDate(strtotime($data['dateValid']));
    //         $dateValid = new \DateTime();
    //         date_date_set($dateValid, $mydate['year'], $mydate['mon'], $mydate['mday']);
    
    //         $userValid->setActiveAt($dateValid);

    //         $entityManager->persist($userValid);
    //         $entityManager->flush();
 
    //         return $this->json([
    //             'message' => 'success',
    //         ]);
    //     }

    //     return $this->json([
    //         'message'=>'non'
    //     ]);
    // }

    #[Route('/app/user/list', name: 'listUser')]
    public function recupListUser(Request $request, UserRepository $userRepository, ChildrenRepository $childrenRepository,EntityManagerInterface $entityManager): JsonResponse
    {
        $user= $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $listUser = $userRepository->findAll();
        foreach($listUser as $userOne){
            $actif = 0;
            $inactif = 0;
            $compteur = 0;
            $childrens = $userOne->getChildrens();
            foreach($childrens as $children){
                $compteur += 1;
                if($children->isIsActive() === true){
                    $actif += 1;
                }elseif($children->isIsActive() === false){
                    $inactif += 1;
                }
            }
            if($compteur > 0){
                if ($actif > 0 && $inactif === 0){
                    $userOne->setAllActif(true);
                    $userOne->setAllInactif(false);
                }elseif ($inactif > 0 && $actif === 0){
                    $userOne->setAllInactif(true);
                    $userOne->setAllActif(false);
                }elseif ($inactif > 0 && $actif > 0){
                    $userOne->setAllActif(true);
                    $userOne->setAllInactif(true);
                }else{
                    $userOne->setAllActif(false);
                    $userOne->setAllInactif(false);
                }
            }else{
                $userOne->setAllActif(Null);
                $userOne->setAllInactif(Null);
            }

            $entityManager->flush();
        }
        

        $data = json_decode($request->getContent(), true);
        if ($data){

            $select = $data['select'];
            $select2 = $data['select2'];
            
            if($user->getRoles() === ['ROLE_ADMIN']){
                
                if ($select === 'Administrateurs'){
                    if($select2 === 'Actifs'){
                        $list = $userRepository->findByRoles(['["ROLE_ADMIN"]'],'actifs');
                    }elseif($select2 === 'Inactifs'){
                        $list = $userRepository->findByRoles(['["ROLE_ADMIN"]'],'inactifs');
                    }else{
                        $list = $userRepository->findByRoles(['["ROLE_ADMIN"]'],null);
                    }
                }elseif($select === 'Intervenants'){
                    if($select2 === 'Actifs'){
                        $list = $userRepository->findByRoles(['["ROLE_INTER"]'],'actifs');
                    }elseif($select2 === 'Inactifs'){
                        $list = $userRepository->findByRoles(['["ROLE_INTER"]'],'inactifs');
                    }else{
                        $list = $userRepository->findByRoles(['["ROLE_INTER"]'],null);
                    }
                }elseif($select === 'Utilisateurs'){
                    if($select2 === 'Actifs'){
                        $list = $userRepository->findByRoles(['["ROLE_USER"]'],'actifs');
                    }elseif($select2 === 'Inactifs'){
                        $list = $userRepository->findByRoles(['["ROLE_USER"]'],'inactifs');
                    }else{
                        $list = $userRepository->findByRoles(['["ROLE_USER"]'],null);
                    }
                }else{
                    if($select2 === 'Actifs'){
                        $list = $userRepository->findByRoles(null,'actifs');
                    }elseif($select2 === 'Inactifs'){
                        $list = $userRepository->findByRoles(null,'inactifs');
                    }else{
                        $list = $userRepository->findByRoles(null,null);
                    }
                }
            }

            $listSend = [];

            foreach($list as $listOne){
                array_push($listSend,[
                    'id'=>$listOne->getId(),
                    'name'=> $listOne->getLastname(),
                    'firstname'=>$listOne->getFirstname(),
                    'role'=>$listOne->getRoles()
                ]);
            }

            return $this->json($listSend);
            
        }

        return $this->json([
            'message'=>'erreur'
        ]);
        // , JsonResponse::HTTP_UNAUTHORIZED);
    }


    #[Route('/app/user/role', name: 'role_user')]
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

    // #[Route('/addChildren/user', name: 'children_user')]
    // public function addChildrenUser(EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): JsonResponse
    // {
    //     $userConnect = $this->getUser();
    //     $data = json_decode($request->getContent(), true);

    //     if (null === $userConnect) {
    //         return $this->json([
    //             'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
    //         ]);
    //     }

    //     $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

    //     $children = new Children();
    //     $children->setName($data['name']);

    //     if($data['birthdate'] == "15"){
    //         $birthdate = new \DateTime();
    //     }else{
    //         $mydate = getDate(strtotime($data['birthdate']));
    //         $birthdate = new \DateTime();
    //         date_date_set($birthdate, $mydate['year'], $mydate['mon'], $mydate['mday']);
    //     }
        
    //     $children->setBirthdate($birthdate);
    //     $children->setParent($user);

    //     $entityManager->persist($children);
    //     $entityManager->flush();

    //     $profil=false;

    //     if ($user->isIsActive() === false) {
    //         if (
    //             $user->getLastname() and 
    //             $user->getFirstname() and
    //             $user->getBirthdate() and
    //             $user->getAddress() and
    //             $user->getZipcode() and
    //             $user->getCity() and
    //             $user->getPhone()
    //         ){
    //             $profil = true;
    //         }
    //     }

    //     $childrenArray=[];
    //     $childrens = $user->getChildrens();
    //     foreach($childrens as $children){
    //         array_push($childrenArray,['name'=>$children->getName(),'birthdate'=>$children->getBirthdate()]);
    //     }


    //     $userSend = [
    //         'id'=>$user->getId(),
    //         'email'=> $user->getEmail(),
    //         'userIdentifier'=>$user->getEmail(),
    //         'username'=>$user->getEmail(),
    //         'roles'=> $user->getRoles(),
    //         'lastname'=> $user->getLastname(),
    //         'firstname'=> $user->getFirstname(),
    //         'birthdate'=> $user->getBirthdate(),
    //         'avatar'=> $user->getAvatar(),
    //         'address'=> $user->getAddress(),
    //         'zipcode'=>$user->getZipcode(),
    //         'city'=>$user->getCity(),
    //         'phone'=>$user->getPhone(),
    //         'subcribeAt'=> $user->getSubcribeAt(),
    //         'isActive'=> $user->isIsActive(),
    //         'activeAt'=> $user->getActiveAt(),
    //         'children'=>$childrenArray
    //     ];

    //     return $this->json([
    //         'user'  => $userSend,
    //         'profil' => $profil
    //     ]);
    // }
}
