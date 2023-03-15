<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ActualiteRepository $actualiteRepository, PartnerRepository $partnerRepository): Response
    {
        $listActualite = $actualiteRepository->find3last();
        
        if($listActualite === []){
            array_push($listActualite,
                [
                    'id' => 0,
                    'title' => "Pas d'actualité",
                    'description' => "Actualité ajoutée s'affiche ici",
                    'date' => Date('now'),
                    'author' => "Pas d'auteur",
                ]
            );
        }

        $partners =$partnerRepository->findAll();

        return $this->render('home/index.html.twig',[
            'listActualite'=> $listActualite,
            'partners'=>$partners
        ]);
    }
}

