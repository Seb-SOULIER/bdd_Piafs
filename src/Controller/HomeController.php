<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\CompteurAdminRepository;
use App\Repository\PartnerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ActualiteRepository $actualiteRepository, PartnerRepository $partnerRepository, CompteurAdminRepository $countUploadRepository): Response
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

        $countUpload = $countUploadRepository->findAll()[0];

        return $this->render('home/index.html.twig',[
            'listActualite'=> $listActualite,
            'partners'=>$partners,
            'countUpload'=>$countUpload
        ]);
    }

    #[Route('/countUpload', name: 'app_count_upload')]
        public function countUpload(CompteurAdminRepository $countUploadRepository): JsonResponse
    {
        $countUpload = $countUploadRepository->findAll()[0];

        $countUpload->setCountUploadApp($countUpload->getCountUploadApp()+1);
        $countUploadRepository->save($countUpload, true);

        return $this->json(
            $countUpload
        );
    }
}

