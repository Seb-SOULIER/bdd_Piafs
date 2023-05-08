<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ActiveUser;
use App\Form\ResetPasswordFormType;
use App\Form\RestorePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    #[Route('/app/login', name: 'api_login')]
    public function index(ActiveUser $activeUser): JsonResponse
    {
        $activeUser->inactiveUser();

        $user= $this->getUser();

        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ]);
        }

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            // 'firstname' => $user->getFirstname(),
            // 'lastname' => $this->$user->getLastname(),
            'role' => $user->getRoles(),
        ]);
    }

    #[Route('/app/login/restore/send', name: 'send_restore_login')]
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
                'user'=>$data['email']
            ]);
        }
        
        return $this->json([
            'error'=>'Il n y a pas de compte avec ce mail : '.$data['email'] 
        ]);
    }

    #[Route('/app/login/restore', name: 'restore_login')]
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
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


    #[Route('/login/restore/send', name: 'send_restore_password')]
    public function sendRestorePassword(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerInterface $mailer,
        ): Response
    {   
        
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy(['email'=>$form->getData('email')]);

            $image = base64_encode(file_get_contents($this->getParameter('kernel.project_dir') . '/assets/images/logo_les_piafs_actifs.png'));

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

                $this->addFlash('success','Mail avec code pour réinitialiser le mot de passe envoyé');

                return $this->redirectToRoute('restore_password', ['user'=> $user->getId()], Response::HTTP_SEE_OTHER);

            } else {
                $this->addFlash('danger','Ce mail n\'est pas enregistré');
            }
        }
        
        return $this->render('registration/registerPassword.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }


    #[Route('/login/restore/{user}', name: 'restore_password')]
    public function restorePassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        User $user
        ): Response
    {   
        $form = $this->createForm(RestorePasswordFormType::class);
        $form->handleRequest($request);


        if ($user->getRestoreCode()){
            if ($form->isSubmitted() && $form->isValid()) {
                if($user->getRestoreCode() == $form->getData()['code']){
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->getData()['password']
                        )
                    );
                    $user->setRestoreCode(Null);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success','Mot de passe réinitialisé avec succès');

                    return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
                }
                $this->addFlash('danger','Le code est incorrecte!');
            }   
        }
        
        return $this->render('registration/restorePassword.html.twig', [
            'restoreForm' => $form->createView(),
        ]);
    }    
}
