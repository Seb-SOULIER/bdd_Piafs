<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\Children;
use App\Entity\Comment;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use App\Repository\ChildrenRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

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

    #[Route('/atelier/list', name: 'site_list_atelier')]
    public function siteListAtelier(AtelierRepository $atelierRepository): Response
    {   
        $now = new DateTime('now');
        $now->sub(new DateInterval('P1D'));

        return $this->render('atelier/index.html.twig', [
            'listAteliers' => $atelierRepository->findAllAfter($now)
        ]);
    }

    #[Route('/atelier/{atelier}', name: 'site_atelier')]
    public function siteAtelier(    Atelier $atelier,
                                    Request $request,
                                    ChildrenRepository $childrenRepository,
                                    EntityManagerInterface $entityManager,
                                    UserRepository $userRepository): Response
    {   
        $user= $this->getUser();

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);
        
        $childrens = [];

        foreach ($user->getChildrens() as $children){
            if($children->isIsActive() === true){
                array_push($childrens,[
                    $children->getFirstname(). ' '. $children->getName() => $children->getId()
                ]);
            }
        }
        
        $form = $this->createFormBuilder() 
            ->add('childrens', ChoiceType::class, [
                'choices'  => $childrens,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('comment',TextareaType::class,[
                'required'=>false,
                'attr'=>[
                    'rows'=>4,
                    'class'=>'input-comment'
                ]
            ])
            ->getForm();
        
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $alreadyRegistered = false;
            foreach($data['childrens'] as $childrenId) {

                $alreadyRegistered = false;

                $children = $childrenRepository->findOneBy(['id'=>$childrenId]);
                
                    foreach($atelier->getParticipants() as $participant) {
                        if($participant === $children){
                            $alreadyRegistered = true;
                        }
                    }

                if($alreadyRegistered){
                    $this->addFlash('danger', $children->getName() . ' ' . $children->getFirstname() . ' est déjà inscrit(e).');
                }else{
                    if($atelier->getPlaceReserved()+1 > $atelier->getPlace()){
                        $this->addFlash('danger', 'Il n\'y a plus de place disponible');
                    }else{
                        $atelier->setPlaceReserved($atelier->getPlaceReserved()+1);
                        $atelier->addParticipant($children);
                        $this->addFlash('success', $children->getName() . ' ' . $children->getFirstname() . ' est inscrit(e).');
                    }   
                }
            }
            if($data['comment'] !== null){
                $comment = new Comment;
                $comment->setIntervenant($atelier->getIntervenant());
                $comment->setAddAt(new DateTimeImmutable());
                $comment->setAdherant($children);
                $comment->setComment($data['comment']);
                $comment->setAtelierAt($atelier->getDate());
                $entityManager->persist($comment);
                $this->addFlash('success','Message transmis à l\'intervenant');
            }

            $entityManager->flush();
            
            return $this->redirectToRoute('site_inscriptions', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('atelier/inscription.html.twig', [
            'atelier' => $atelier,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/inscriptions', name: 'site_inscriptions')]
    public function siteInscriptions(UserRepository $userRepository): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        return $this->render('atelier/mes_inscriptions.html.twig', [
            'user'=>$user
        ]);

    } 

    #[Route('/atelier/inter/list', name: 'site_list_inter_atelier')]
    public function siteListInterAtelier(   AtelierRepository $atelierRepository,
                                            UserRepository $userRepository
                                            ): Response
    {   
        $now = new DateTime('now');
        $now->sub(new DateInterval('P1D'));

        $userConnect = $this->getUser();
        
        if (null === $userConnect) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        if ($user->getRoles() == ['ROLE_ADMIN']){
            $ateliers = $atelierRepository->findByAdmin();
        }else{
            $ateliers = $atelierRepository->findByInter($user);
        }

        return $this->render('atelierInter/index.html.twig', [
            'listAteliers' => $ateliers
        ]);
    }

    #[Route('/atelier/inter/new', name: 'crud_atelier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AtelierRepository $atelierRepository): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $dateAtelier = $form->getData()->getDate();
            $hourStart = $form->getData()->getHourStart();
            $hourStop = $form->getData()->getHourStop();

            $hourStart->setDate($dateAtelier->format('Y'), $dateAtelier->format('m'), $dateAtelier->format('d'));
            $hourStop->setDate($dateAtelier->format('Y'), $dateAtelier->format('m'), $dateAtelier->format('d'));

            $atelier->setHourStart($hourStart);
            $atelier->setHourStop($hourStop);

            $atelier->setIntervenant($this->getUser());

            $ateliersMemeJour = $atelierRepository->findBy(['date' => $dateAtelier]);

            foreach($ateliersMemeJour as $atelierMemeJour){
                if( (($atelier->getHourStart() >= $atelierMemeJour->getHourStart()) && ($atelier->getHourStart() <  $atelierMemeJour->getHourStop())) or (($atelier->getHourStop() > $atelierMemeJour->getHourStart()) && ($atelier->getHourStop() <=  $atelierMemeJour->getHourStop()) )){
                    $this->addFlash('danger','Impossible d\'ajouter l\'atelier '. $atelier->getName() . ', il est en même temp que l\'atelier '. $atelierMemeJour->getName(). ' qui a lieu de : ' . $atelierMemeJour->getHourStart()->format('H:i') . ' à '. $atelierMemeJour->getHourStop()->format('H:i'));

                    return $this->renderForm('atelierInter/new.html.twig', [
                        'atelier' => $atelier,
                        'form' => $form,
                    ]);
                }
            }

            $atelierRepository->save($atelier, true);
            $this->addFlash('success','Atelier '. $atelier->getName() . ' ajouté avec succés.' );

            return $this->redirectToRoute('site_list_inter_atelier', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('atelierInter/new.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/atelier/inter/{id}/edit', name: 'crud_atelier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, AtelierRepository $atelierRepository): Response
    {
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $dateAtelier = $form->getData()->getDate();
            $hourStart = $form->getData()->getHourStart();
            $hourStop = $form->getData()->getHourStop();

            $hourStart->setDate($dateAtelier->format('Y'), $dateAtelier->format('m'), $dateAtelier->format('d'));
            $hourStop->setDate($dateAtelier->format('Y'), $dateAtelier->format('m'), $dateAtelier->format('d'));

            $atelier->setHourStart($hourStart);
            $atelier->setHourStop($hourStop);

            $atelier->setIntervenant($this->getUser());

            $ateliersMemeJour = $atelierRepository->findBy(['date' => $dateAtelier]);

            $ateliersMemeJour = array_filter($ateliersMemeJour, function ($element) use ($atelier) {
                return $element !== $atelier;
            });

            foreach($ateliersMemeJour as $atelierMemeJour){
                if( (($atelier->getHourStart() >= $atelierMemeJour->getHourStart()) && ($atelier->getHourStart() <  $atelierMemeJour->getHourStop())) or (($atelier->getHourStop() > $atelierMemeJour->getHourStart()) && ($atelier->getHourStop() <=  $atelierMemeJour->getHourStop()) )){
                    $this->addFlash('danger','Impossible d\'ajouter l\'atelier '. $atelier->getName() . ', il est en même temp que l\'atelier '. $atelierMemeJour->getName(). ' qui a lieu de : ' . $atelierMemeJour->getHourStart()->format('H:i') . ' à '. $atelierMemeJour->getHourStop()->format('H:i'));

                    return $this->renderForm('atelierInter/new.html.twig', [
                        'atelier' => $atelier,
                        'form' => $form,
                    ]);
                }
            }

            $atelierRepository->save($atelier, true);
            $this->addFlash('success','Atelier '. $atelier->getName() . ' modifié avec succés.' );

            return $this->redirectToRoute('site_list_inter_atelier', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('atelierInter/edit.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/atelier/inter/profil/{id}/atelier/{atelier}', name: 'crud_atelier_profil', methods: ['GET', 'POST'])]
    public function profil(Children $children,Atelier $atelier, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['id' => $children->getParent()]);
    
        return $this->renderForm('atelierInter/profil.html.twig', [
            'user'=>$user,
            'children'=>$children,
            'atelier'=>$atelier
        ]);
    }

    #[Route('/atelier/inter/{atelier}/desinscription/{children}', name: 'crud_atelier_unsubscrible', methods: ['GET', 'POST'])]
    public function uunsubscible(Atelier $atelier, Children $children, AtelierRepository $atelierRepository): Response
    {
        $atelier->removeParticipant($children);
        $atelier->setPlaceReserved(count($atelier->getParticipants()));
        $atelierRepository->save($atelier, true);

        $this->addFlash('success', $children->getName() .' '. $children->getFirstname(). ' n\'est plus inscrit à l\'atelier ' . $atelier->getName() . '.' );
               
        return $this->redirectToRoute('site_list_inter_atelier', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/atelier/inter/delete/{atelier}', name: 'crud_atelier_delete', methods: ['GET', 'POST'])]
    public function delete(Atelier $atelier, AtelierRepository $atelierRepository): Response
    {
        if (count($atelier->getParticipants()) > 0 ){
            $this->addFlash('danger', 'Il y a des inscrits à l\'atelier, il est donc impossible de le supprimer.' );
            return $this->redirectToRoute('site_list_inter_atelier', [], Response::HTTP_SEE_OTHER);            
        }

        $this->addFlash('success', 'Atelier supprimé avec succés' );
        return $this->redirectToRoute('site_list_inter_atelier', [], Response::HTTP_SEE_OTHER);
    }

}
