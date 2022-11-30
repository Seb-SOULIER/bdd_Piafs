<?php

namespace App\Controller;

use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login')]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $user= $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='); // somehow create an API token for $user
        $validToken = new DateTime();
        $validToken->add(new DateInterval('PT1H'));

        $user->setToken($token);
        $user->setValidToken($validToken);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'role' => $user->getRoles(),
            'token' => $token,
            'validToken'=>$validToken
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(EntityManagerInterface $entityManager)
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
