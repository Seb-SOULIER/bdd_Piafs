<?php

namespace App\Controller;

use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/api/sendRestoreLogin', name: 'send_restore_login')]
    public function sendRestoreLogin(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerInterface $mailer,
        ): JsonResponse
    {   
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['email'=>$data['email']]);

        $image = base64_encode(file_get_contents($this->getParameter('kernel.project_dir') . '/assets/images/logo_les_piafs_actifs_2.png'));

        if($user){
            $user-> setRestoreCode(random_int(100000, 999999));
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new TemplatedEmail())
                ->from('les_piafs_actifs@cod4y.fr')
                ->to($user->getEmail())
                ->subject('Reinitialisation du mot de passe')
                ->htmlTemplate('email/resetPassword.html.twig')
                ->context(compact('user','image'));
            $mailer->send($email);

            return $this->json([
                'success'=>$data['email']
            ]);
        }
        
        return $this->json([
            'error'=>'Il n y a pas de compte avec ce mail : '.$data['email'] 
        ]);
    }

    #[Route('/api/restoreLogin', name: 'restore_login')]
    public function restoreLogin(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ): JsonResponse
    {   
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->findOneBy(['email'=>$data['email']]);

        if($user){
            if($user->getRestoreCode()){
                if($user->getRestoreCode() === $data['restoreCode']){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $data['password']
                        )
                    );
                    $user->setRestoreCode(null);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    return $this->json([
                        'success'=>'OK'
                    ]);     
                }
            }
        }
        return $this->json([
            'error'=>'Mot de passe pas reinitialise'
        ]);
    }    

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(EntityManagerInterface $entityManager)
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
