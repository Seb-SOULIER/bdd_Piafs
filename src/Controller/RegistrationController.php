<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );
        $user->setToken(null);
        $user->setValidToken(null);
        $user->setLastname($data['lastname']);
        $user->setFirstname($data['firstname']);
        // $user->setBirthdate($data['birthdate']);
        $user->setAvatar(null);
        $user->setAddress($data['address']);
        $user->setZipcode($data['zipcode']);
        $user->setCity($data['city']);
        $user->setPhone($data['phone']);
        $user->setIsActive(false);
        $user->setRestoreCode(null);
        
        $errors = $validator->validate($user);

        if(count($errors)>0){
            return $this->json([
                "error" => $errors[0]->getMessage()
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'role' => $user->getRoles(),
        ]);
    }
}
