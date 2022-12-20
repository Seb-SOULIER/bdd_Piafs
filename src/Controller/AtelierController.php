<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Repository\AtelierRepository;
use App\Repository\ChildrenRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AtelierController extends AbstractController
{
    #[Route('/atelier/add', name: 'add_atelier')]
    public function addAtelier(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $data = json_decode($request->getContent(), true);

        if($user->getRoles() === ['ROLE_INTER'] or $user->getRoles() === ['ROLE_ADMIN']){
            $atelier = new Atelier;
            $atelier->setName($data['name']);
            $atelier->setDescription($data['description']);
            $atelier->setPlace($data['place']);

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
            
            $atelier->setIntervenant($user);

            $entityManager->persist($atelier);
            $entityManager->flush();

            return $this->json([
                'success'=>'ok'
            ]);
        }

        return $this->json([
            'error'=>'non autorisé'
        ]);
    }


    #[Route('/atelier/list', name: 'list_atelier')]
    public function listAtelier(AtelierRepository $atelierRepository): Response
    {   
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $now = new DateTime('now');
        $listBdd = $atelierRepository->findAllAfter($now);
       
        $listBddSend = [];
        
        $dateAtelier = new DateTime();
        $dateAt = $dateAtelier->format('Y-m-d');

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
                        'places'=>$atelier->getPlace()
                    ]
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
                    'places'=>$atelier->getPlace()
                ]];
            }
            $dateAt = $dateAtAtelier;
        }

        $utilbdd = $user->getChildrens();

        $utilisateurs = [];

        foreach($utilbdd as $util) {
            array_push($utilisateurs,[
                'id' => $util->getId(),
                'name' => $util->getName()
            ]);
        }

        return $this->json([
            "listAteliers" => $listBddSend,
            "utilisateurs" => $utilisateurs
        ]);
    }

    #[Route('/atelier/inscription', name: 'inscription_atelier')]
    public function inscriptionAtelier(AtelierRepository $atelierRepository, Request $request, EntityManagerInterface $entityManager,ChildrenRepository $childrenRepository): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        if ($user->isIsActive() === true){
            $data = json_decode($request->getContent(), true);
            
            $atelier = $atelierRepository->findOneBy(['id'=>$data['id']]);

            $children = $childrenRepository->findOneBy(['id'=>$data['children']]);

            $alreadyRegistered = false;

            foreach($atelier->getParticipants() as $OneParticipant){
                if($OneParticipant === $children){
                    $alreadyRegistered = true;
                }
            }
                
            if ($alreadyRegistered){
                return $this->json([
                    "error" => 'Déjà inscrit à l\'atelier'
                ]);
            }

            $atelier->addParticipant($children);

            $entityManager->persist($atelier);
            $entityManager->flush();

            return $this->json([
                "success" => $user->getLastname(),
            ]);
        }else{
            return $this->json([
                "error"=>"Compte non activé"
            ]);
        }

        return $this->json([
            "error"=>"Ajout non autorisé"
        ]);
    }

    #[Route('/inscription/user', name: 'inscription_user')]
    public function inscriptionUser(AtelierRepository $atelierRepository): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        // recupere tous les ateliers
        $ateliers = $atelierRepository->findByUser();

        $reservationSend=[];

        // Pour chaque atelier
        foreach ($ateliers as $atelier){
            
            $participantArray=[];
            
            foreach($atelier->getParticipants() as $participant){
                if($participant->getParent() === $user){
                    array_push($participantArray,['name'=>$participant->getName(),'id'=>$participant->getId()]);
                }
            }
           
            if(!empty($participantArray)){
                array_push($reservationSend, [
                    'title'=>$atelier->getDate(),
                    'id'=>$atelier->getId(),
                    'data'=> [[
                        "atelier"=>$atelier->getName(),
                        "id"=>$atelier->getId(),
                        "intervenant"=>$atelier->getIntervenant()->getLastname(). " " . $atelier->getIntervenant()->getFirstname(),
                        "dateStart"=>$atelier->getHourStart(),
                        "dateStop"=>$atelier->getHourStop(),
                        "participant"=> $participantArray
                    ]]
                ]);
            }
        }
        
        return $this->json([
            'section'=> $reservationSend,
        ]);
    }

    #[Route('/atelier/unsubscibe', name: 'unsubscibe_atelier')]
    public function unsubscibeAtelier(Request $request, AtelierRepository $atelierRepository, ChildrenRepository $childrenRepository, EntityManagerInterface $entityManager): Response
    {
        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }

        $data = json_decode($request->getContent(), true);

        $atelier = $atelierRepository->findById($data['atelier']);
        $children = $childrenRepository->findById($data['participant']);

        if (!empty($children) and !empty($atelier)){
            $atelier[0]->removeParticipant($children[0]);
            $entityManager->flush();
        }

        return $this->json([
            'error'=> 'success',
        ]);
    }
}
