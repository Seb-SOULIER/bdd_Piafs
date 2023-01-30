<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class ActualiteController extends AbstractController
{
    #[Route('/app/actualite', name: 'app_actualite')]
    public function index(ActualiteRepository $actualiteRepository): Response
    {
        $listActualite = $actualiteRepository->find3last();
        $listActualiteSerialize = [];
        
        foreach($listActualite as $actualite) {
            array_push($listActualiteSerialize,
                [
                    'id' => $actualite->getId(),
                    'title' => $actualite->getTitle(),
                    'description' => $actualite->getDescription(),
                    'date' => $actualite->getDate(),
                    'author' => $actualite->getAuthor()->getLastname(). ' ' . $actualite->getAuthor()->getFirstname(),
                ]
            );
        }

        if($listActualiteSerialize === []){
            array_push($listActualiteSerialize,
                [
                    'id' => 0,
                    'title' => "Pas d'actualité",
                    'description' => "Actualité ajoutée s'affiche ici",
                    'date' => Date('now'),
                    'author' => "Pas d'auteur",
                ]
            );
        }

        return $this->json(
            $listActualiteSerialize,
        );
    }
}
