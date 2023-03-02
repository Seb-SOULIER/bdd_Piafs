<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Comment;
use App\Repository\AtelierRepository;
use App\Repository\ChildrenRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class AtelierController extends AbstractController
{
    #[Route('/app/atelier/add', name: 'add_atelier')]
    public function addAtelier( Request $request,
                                EntityManagerInterface $entityManager,
                                AtelierRepository $atelierRepository,
                                UserRepository $userRepository): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'error' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $data = json_decode($request->getContent(), true);

        if($user->getRoles() === ['ROLE_INTER'] or $user->getRoles() === ['ROLE_ADMIN']){

            $dateAtel = getDate(strtotime($data['dateAddAtelier']));
            $dateAtelier = new \DateTime();
            date_date_set($dateAtelier, $dateAtel['year'], $dateAtel['mon'], $dateAtel['mday']);
            
            $StartAtel = getDate(strtotime($data['timeStartAddAtelier']));
            $AtelierStartAt = new \DateTime();
            date_date_set($AtelierStartAt, $dateAtel['year'], $dateAtel['mon'], $dateAtel['mday']);
            date_time_set($AtelierStartAt, $StartAtel['hours'], $StartAtel['minutes']);

            $StopAtel = getDate(strtotime($data['timeStopAddAtelier']));
            $AtelierStopAt = new \DateTime();
            date_date_set($AtelierStopAt, $dateAtel['year'], $dateAtel['mon'], $dateAtel['mday']);
            date_time_set($AtelierStopAt, $StopAtel['hours'], $StopAtel['minutes']);


             // verifie si la place est libre
            $allReserved = [];
            $ateliersMemeJour = $atelierRepository->findBy(['date' => $dateAtelier]);

            foreach($ateliersMemeJour as $atelierMemeJour){
                if( ($AtelierStartAt >= $atelierMemeJour->getHourStart()) && ($AtelierStartAt <  $atelierMemeJour->getHourStop()) ){
                    array_push($allReserved,$atelierMemeJour);
                }
                if( ($AtelierStopAt > $atelierMemeJour->getHourStart()) && ($AtelierStopAt <=  $atelierMemeJour->getHourStop()) ){
                    array_push($allReserved,$atelierMemeJour);
                }
            }

            
            if($allReserved){
                return $this->json([
                    'error'=> 'Impossible d\'ajouter l\'atelier, l\'atelier "'
                    . $allReserved[0]->getName()
                    . ' a lieu le '. $allReserved[0]->getDate()->format('d/m/Y') . ' de '
                    . $allReserved[0]->getHourStart() ->format('H:i') . ' à '
                    . $allReserved[0]->getHourStop() ->format('H:i')
                ]);    
            }

            $atelier = new Atelier;
            $atelier->setName($data['name']);
            $atelier->setDescription($data['description']);
            $atelier->setPlace($data['place']);
            $atelier->setPlaceReserved(0);

            $atelier->setDate($dateAtelier);
            $atelier->setHourStart($AtelierStartAt);
            $atelier->setHourStop($AtelierStopAt);
            
            if(array_key_exists('selectedInter', $data)){
                $inter = $userRepository->findOneBy(['id'=>$data['selectedInter']]);
                $atelier->setIntervenant($inter);
            }else{
                $atelier->setIntervenant($user);
            }

            $entityManager->persist($atelier);
            $entityManager->flush();

            return $this->json([
                'success'=>'L\'atelier '. $atelier->getName() . ' est ajouté à la liste des ateliers.'
            ]);
        }

        return $this->json([
            'error'=>'non autorisé'
        ]);
    }

    #[Route('/app/atelier/edit', name: 'edit_atelier')]
    public function editAtelier(Request $request, EntityManagerInterface $entityManager, AtelierRepository $atelierRepository): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'error' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $data = json_decode($request->getContent(), true);

        if($user->getRoles() === ['ROLE_INTER'] or $user->getRoles() === ['ROLE_ADMIN']){
            $atelier = $atelierRepository->findOneBy(['id'=>$data['id']]);

            $atelier->setName($data['name']);
            $atelier->setDescription($data['description']);
            $atelier->setPlace($data['place']);
            $atelier->setPlaceReserved(0);

            $dateAtel = getDate(strtotime($data['dateAddAtelier']));
            $dateAtelier = new \DateTime();
            date_date_set($dateAtelier, $dateAtel['year'], $dateAtel['mon'], $dateAtel['mday']);

            $atelier->setDate($dateAtelier);

            $StartAtel = getDate(strtotime($data['timeStartAddAtelier']));
            $AtelierStartAt = new \DateTime();
            date_date_set($AtelierStartAt, $dateAtel['year'], $dateAtel['mon'], $dateAtel['mday']);
            date_time_set($AtelierStartAt, $StartAtel['hours'], $StartAtel['minutes']);

            $atelier->setHourStart($AtelierStartAt);

            $StopAtel = getDate(strtotime($data['timeStopAddAtelier']));
            $AtelierStopAt = new \DateTime();
            date_date_set($AtelierStopAt, $dateAtel['year'], $dateAtel['mon'], $dateAtel['mday']);
            date_time_set($AtelierStopAt, $StopAtel['hours'], $StopAtel['minutes']);

            $atelier->setHourStop($AtelierStopAt);

            // $entityManager->persist($atelier);
            $entityManager->flush();

            return $this->json([
                'success'=>'Modification de l\'atelier "'. $atelier->getName() . '" réalisé avec succès.'
            ]);
        }

        return $this->json([
            'error'=>'non autorisé'
        ]);
    }

    #[Route('/app/atelier/delete', name: 'delete_atelier')]
    public function deleteAtelier(Request $request, EntityManagerInterface $entityManager, AtelierRepository $atelierRepository): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'error' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $data = json_decode($request->getContent(), true);
        $success="";
        $error="";

        if($user->getRoles() === ['ROLE_INTER'] or $user->getRoles() === ['ROLE_ADMIN']){
            $atelier = $atelierRepository->findOneBy(['id'=>$data['id']]);
            
            if(count($atelier->getParticipants()) > 0){
                $error = "Impossible de supprimer l'atelier, il reste des inscrits!";
            }else{
                $success = 'L\'atelier "'. $atelier->getName() . '" a été supprimé avec succès.';
                $entityManager->remove($atelier);
                $entityManager->flush();
            }
        }
        return $this->json([
            "success" => $success,
            "error" => $error
        ]);
    }

    #[Route('/app/atelier/list', name: 'list_atelier')]
    public function listAtelier(AtelierRepository $atelierRepository): Response
    {   
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $now = new DateTime('now');
        $now->sub(new DateInterval('P1D'));
        
        $listBdd = $atelierRepository->findAllAfter($now);
       
        $listBddSend = [];
        
        $dateAt = $now->format('Y-m-d');

        foreach($listBdd as $atelier) {
            $dateAtAtelier = $atelier->getDate()->format('Y-m-d');
            if($dateAtAtelier === $dateAt){
                array_push($listBddSend[$dateAtAtelier], 
                    [
                        'id' => $atelier->getId(),
                        'name' => $atelier->getName(),
                        'description' => $atelier->getDescription(),
                        'date' => $atelier->getDate(),
                        'hoursStart' => $atelier->getHourStart(),
                        'hoursStop' => $atelier->getHourStop(),
                        'intervenant' => $atelier->getIntervenant()->getLastname(). " " . $atelier->getIntervenant()->getFirstname(),
                        'places'=>$atelier->getPlace(),
                        'placesReserved'=>$atelier->getPlaceReserved(),
                    ],
                );
            }else{
                $listBddSend[$dateAtAtelier] = [[
                    'id' => $atelier->getId(),
                    'name' => $atelier->getName(),
                    'description' => $atelier->getDescription(),
                    'date' => $atelier->getDate(),
                    'hoursStart' => $atelier->getHourStart(),
                    'hoursStop' => $atelier->getHourStop(),
                    'intervenant' => $atelier->getIntervenant()->getLastname(). " " . $atelier->getIntervenant()->getFirstname(),
                    'places'=>$atelier->getPlace(),
                    'placesReserved'=>$atelier->getPlaceReserved()
                ]];
            }
            $dateAt = $dateAtAtelier;
        }

        return $this->json([
            "listAteliers" => $listBddSend
        ]);
    }


    #[Route('/app/adherant/list', name: 'list_children')]
    public function listchildren(ChildrenRepository $childrenRepository): Response
    {  
        $user= $this->getUser();
        $childrens = $childrenRepository->findBy(['parent'=>$user,'isActive'=>true]);

        $childrensSend = [];
        foreach($childrens as $children) {
            array_push($childrensSend, 
                [
                    'id' => $children->getId(),
                    'name' => $children->getName(),
                    'firstname' => $children->getFirstname(),
                    'birthdate' => $children->getBirthdate(),
                    'isActive' => $children->isIsActive(),
                    'activeAt' => $children->getActiveAt()
                ]
            );
        }

        return $this->json([
            "childrens" => $childrensSend,
 
        ]);
    }

    #[Route('/app/atelier/register', name: 'inscription_atelier')]
    public function inscriptionAtelier( AtelierRepository $atelierRepository,
                                        Request $request,
                                        EntityManagerInterface $entityManager,
                                        ChildrenRepository $childrenRepository
                                        ): Response
    {
        $user= $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $error="";
        $success="";
        $atelier = $atelierRepository->findOneBy(['id'=>$data['atelier']['id']]);

        $childrens = $data['children'];

        foreach ($childrens as $children){
            $childrensArray[$children[0]] = $children[1];
        }

        foreach($childrensArray as $key=>$value) {
            if ($value === true){
                $children = $childrenRepository->findOneBy(['id'=>$key]);
                
                $alreadyRegistered = false;
                foreach($atelier->getParticipants() as $OneParticipant){
                    if($OneParticipant === $children){
                        $alreadyRegistered = true;
                    }
                }

                if($alreadyRegistered){
                    $error = $error . $children->getName() . ' ' . $children->getFirstname() . ' est déjà inscrit.';
                }else{
                    if($atelier->getPlaceReserved()+1 > $atelier->getPlace()){
                        $error = $error . "Il n'y a plus de place disponible";
                    }else{
                        $atelier->setPlaceReserved($atelier->getPlaceReserved()+1);
                        $atelier->addParticipant($children);
                        $success = $success . $children->getName() . ' ' . $children->getFirstname() . ' est inscrit.';

                        if($data['comment'] !== ""){
                            $comment = new Comment;
                            $comment->setIntervenant($atelier->getIntervenant());
                            $comment->setAddAt(new DateTimeImmutable());
                            $comment->setAdherant($children);
                            $comment->setComment($data['comment']);
                            $comment->setAtelierAt($atelier->getDate());
                            $entityManager->persist($comment);
                        }
                    }
                }
            }
        }

        $entityManager->flush();

        if ($error){
            $response = ['error' => $error];
        }else{
            $response = ["success" => $success];
        }

        return $this->json($response);
    }
    
    
    #[Route('/app/atelier/unregister', name: 'desinscription_atelier')]
    public function desinscriptionAtelier( AtelierRepository $atelierRepository,
                                        Request $request,
                                        EntityManagerInterface $entityManager,
                                        ChildrenRepository $childrenRepository
                                        ): Response
    {
        $user= $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $success="";
        $atelier = $atelierRepository->findOneBy(['id'=>$data['idAtelier']]);

        $children = $childrenRepository->findOneBy(['id'=>$data['idParticipant']]);

        $atelier->removeParticipant($children);
        $atelier->setPlaceReserved($atelier->getPlaceReserved()-1);

        $success="";
        $success = $success . $children->getName() . ' ' . $children->getFirstname() . ' est désincrit.';

        $entityManager->flush();

        return $this->json([
            "success" => $success
        ]);
    }

    #[Route('/app/atelier/registerUser', name: 'inscription_user')]
    public function inscriptionUser(UserRepository $userRepository): Response
    {
        $userConnect = $this->getUser();
        
        if (null === $userConnect) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        // recupere tous les adherants
        $adherants = $user->getChildrens();

        $reservationSend=[];

        foreach($adherants as $adherant){
            if ($adherant->isIsActive()){
                
                $ateliersArray=[];
                $ateliers = $adherant->getAteliers();

                foreach($ateliers as $atelier){
                    if($atelier->getDate() > new DateTime()){
                        array_push($ateliersArray,[
                            'id'=>$atelier->getId(),
                            'title'=>$atelier->getName(),
                            'date'=>$atelier->getDate(),
                            'hourStart'=>$atelier->getHourStart(),
                            'hourStop'=>$atelier->getHourStop(),
                            'description'=>$atelier->getDescription(),
                            'place'=>$atelier->getPlace(),
                            'PlaceReserved'=>$atelier->getPlaceReserved(),
                            'intervenantNom'=>$atelier->getIntervenant()->getLastname(),
                            'intervenantPrenom'=>$atelier->getIntervenant()->getFirstname()
                        ]);
                    }
                }

                if ($ateliersArray !== []){
                    array_push($reservationSend,[
                        'id'=>$adherant->getId(),
                        'name'=>$adherant->getName(),
                        'firstname'=>$adherant->getFirstname(),
                        'birthdate'=>$adherant->getBirthdate(),
                        'isActive'=>$adherant->isIsActive(),
                        'activeAt'=>$adherant->getActiveAt(),
                        'data'=>$ateliersArray,
                    ]);
                }
            }
        }

        $userSend=[];
        array_push($userSend,[
            'id'=>$user->getId(),
            'email'=>$user->getEmail(),
            'roles'=>$user->getRoles(),
            'lastname'=>$user->getLastname(),
            'firstname'=>$user->getFirstname(),
            'birhdate'=>$user->getBirthdate(),
            'address'=>$user->getAddress(),
            'zipcode'=>$user->getZipcode(),
            'city'=>$user->getCity(),
            'phone'=>$user->getPhone()
        ]);

        $error = "";
        if($reservationSend === []){
            $error = 'Pas de reservation';
        }
        
        if($error){
            return $this->json([
                'error'=> $error
            ]);
        }

        return $this->json([
            'section'=> $reservationSend,
            'user'=>$userSend,
        ]);
    }

    #[Route('/app/admin/atelier/list', name: 'atelier_admin')]
    public function atelierAdmin(   UserRepository $userRepository,
                                    AtelierRepository $atelierRepository,
                                    CommentRepository $commentRepository
                                    ): Response
    {
        $userConnect = $this->getUser();
        
        if (null === $userConnect) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        if ($user->getRoles() == ['ROLE_ADMIN']){
            $ateliers = $atelierRepository->findByAdmin();
        }else{
            $ateliers = $atelierRepository->findByInter($user);
        }

        $ateliersArray=[];

        foreach($ateliers as $atelier){

            $atelierParticipants = [];
            foreach( $atelier->getParticipants() as $participant){
                
                $comments = $commentRepository->findBy(['intervenant'=>$atelier->getIntervenant(),'adherant'=>$participant],['addAt'=>'DESC']);
                $commentsSend=[];
                foreach($comments as $comment){
                    array_push($commentsSend,[
                        'addAt'=>$comment->getAddAt(),
                        'comment'=>$comment->getComment(),
                        'atelierAt'=>$comment->getAtelierAt()
                    ]);
                }

                array_push($atelierParticipants,[
                    'idAtelier'=>$atelier->getId(),
                    'id'=>$participant->getId(),
                    'name'=>$participant->getName(),
                    'firstname'=>$participant->getFirstname(),
                    'birthdate'=>$participant->getBirthdate(),
                    'isActive'=>$participant->isIsActive(),
                    'activeAt'=>$participant->getActiveAt(),
                    'comment'=>$commentsSend,
                    'nbrComment'=>count($comments),
                    
                    'idUser'=>$participant->getParent()->getId(),
                    'email'=>$participant->getParent()->getEmail(),
                    'roles'=>$participant->getParent()->getRoles(),
                    'lastnameUser'=>$participant->getParent()->getLastname(),
                    'firstnameUser'=>$participant->getParent()->getFirstname(),
                    'birthdateUser'=>$participant->getParent()->getBirthdate(),
                    'address'=>$participant->getParent()->getAddress(),
                    'zipcode'=>$participant->getParent()->getZipcode(),
                    'city'=>$participant->getParent()->getCity(),
                    'phone'=>$participant->getParent()->getPhone(),
                    'avatar'=>$participant->getParent()->getAvatar()
                ]);
            }

            array_push($ateliersArray,[
                'id'=>$atelier->getId(),
                'title'=>$atelier->getName(),
                'date'=>$atelier->getDate(),
                'hourStart'=>$atelier->getHourStart(),
                'hourStop'=>$atelier->getHourStop(),
                'description'=>$atelier->getDescription(),
                'place'=>$atelier->getPlace(),
                'PlaceReserved'=>$atelier->getPlaceReserved(),
                'instervenantId'=>$atelier->getIntervenant()->getId(), 
                'intervenantNom'=>$atelier->getIntervenant()->getLastname(),
                'intervenantPrenom'=>$atelier->getIntervenant()->getFirstname(),
                'data'=> $atelierParticipants
            ]);

        }

        $error = "";
        if($ateliersArray === []){
            $error = 'Pas d\'ateliers';
        }
        
        if($error){
            return $this->json([
                'error'=> $error
            ]);
        }

        return $this->json([
            'section'=> $ateliersArray
        ]);
    }

    #[Route('/app/admin/atelier/listInter', name: 'atelier_admin_listInter')]
    public function atelierAdminListInter(UserRepository $userRepository){
        $list1 = $userRepository->findByRoles(['["ROLE_ADMIN"]'],null);
        $list2 = $userRepository->findByRoles(['["ROLE_INTER"]'],null);

        $list = array_merge($list1, $list2);

        $listSend = [];

        foreach($list as $listOne){
            array_push($listSend,[
                'id'=>$listOne->getId(),
                'name'=> $listOne->getLastname(),
                'firstname'=>$listOne->getFirstname(),
            ]);
        }

        return $this->json($listSend);
    }
}
