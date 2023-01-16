<?php

namespace App\Controller;

use App\Entity\Children;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
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

    #[Route('/registerEdit', name: 'register_edit')]
    public function registerEdit(Request $request, EntityManagerInterface $entityManager,ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $user= $this->getUser();
        $user->setLastname($data['lastname']);
        $user->setFirstname($data['firstname']);

        $mydate = getDate(strtotime($data['birthdate']));
        $date = new \DateTime();
        date_date_set($date, $mydate['year'], $mydate['mon'], $mydate['mday']);
        $user->setBirthdate($date);
        $user->setAddress($data['address']);
        $user->setZipcode($data['zipcode']);
        $user->setCity($data['city']);
        $user->setPhone($data['phone']);
        
        $entityManager->persist($user);
        $entityManager->flush();

        // $user->setAvatar($data['avatar']);

        return $this->json([
            'user'  => $user->getEmail()
        ]);
    }

    #[Route('/registerChildrenEdit', name: 'register_children_edit')]
    public function registerChildrenEdit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $user= $this->getUser();

        $childrens = $user->getChildrens();

        foreach($childrens as $child){
            if($child->getName() == $data['name']){
                if($child->getFirstname() == $data['firstname']){
                    return $this->json([
                        'error' => 'Utilisateur déjà ajouté',
                    ]);
                }
            }
        }

        $children = new Children();
        $children->setName($data['name']);
        $children->setFirstname($data['firstname']);
        $children->setIsActive(false);
        
        $mydate = getDate(strtotime($data['birthdate']));
        $date = new \DateTime();
        date_date_set($date, $mydate['year'], $mydate['mon'], $mydate['mday']);
        $children->setBirthdate($date);

        $user->addChildren($children);
        
        $entityManager->persist($children);
        $entityManager->persist($user);
        $entityManager->flush();

        // $user->setAvatar($data['avatar']);

        return $this->json([
            'success'  => 'ok',
        ]);
    }
}
