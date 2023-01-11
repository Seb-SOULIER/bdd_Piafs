<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActualiteController extends AbstractController
{
    #[Route('/actualite', name: 'app_actualite')]
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

        return $this->json(
            $listActualiteSerialize,
        );
    }
}
