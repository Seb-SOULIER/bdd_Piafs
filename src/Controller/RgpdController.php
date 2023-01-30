<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RgpdController extends AbstractController
{
    #[Route('/app/rgpd/privacy', name: 'app_rgpd_privacy')]
    public function privacy(): Response
    {
        $privacy = "coucou";

        return $this->json(
            $privacy,
        );
    }

    #[Route('/app/rgpd/terms', name: 'app_rgpd_terms')]
    public function terms(): Response
    {
        $terms = "coucou";

        return $this->json(
            $terms,
        );
    }
}
