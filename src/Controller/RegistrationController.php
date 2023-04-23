<?php

namespace App\Controller;

use App\Entity\Children;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\ChildrenRepository;
use App\Repository\UserRepository;
use App\Security\LoginAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/app/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        if (isset($data['firstname']) && !empty($data['firstname']) ) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['lastname']) && !empty($data['lastname']) ) {
            $user->setLastname($data['lastname']);
        }
        $user->setEmail($data['email']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );
        $user->setAvatar(rand(1,15).".png");

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

    #[Route('/app/user/edit', name: 'register_edit')]
    public function registerEdit(
                                    Request $request,
                                    EntityManagerInterface $entityManager,
                                    ValidatorInterface $validator,
                                    UserRepository $userRepository
                                    ): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $userConnect= $this->getUser();

        if (null === $userConnect) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }
        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        $user->setLastname(strtoupper($data['lastname']));
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


        return $this->json([
            'user'  => $user->getEmail()
        ]);
    }

    #[Route('/app/admin/user/edit', name: 'register_admin_edit')]
    public function registerAdminEdit(
                                        Request $request,
                                        EntityManagerInterface $entityManager,
                                        UserRepository $userRepository
                                    ): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $user= $userRepository->findOneBy(['id'=>$data['id']]);
        $user->setLastname(strtoupper($data['lastname']));
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

    #[Route('/app/adherant/edit', name: 'register_children_edit')]
    public function registerChildrenEdit(   Request $request,
                                            EntityManagerInterface $entityManager,
                                            UserRepository $userRepository
                                            ): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $userConnect= $this->getUser();

        if (null === $userConnect) {
            return $this->json([
                'message' => 'Erreur Utilisateur - Merci de vous reconnecter',
            ]);
        }
        $user = $userRepository->findOneBy(['id'=>$this->getUser()]);

        $childrens = $user->getChildrens();

        foreach($childrens as $child){
            if(strtolower($child->getName()) === strtolower($data['name'])){
                if(strtolower($child->getFirstname()) === strtolower($data['firstname'])){
                    return $this->json([
                        'error' => 'Utilisateur déjà ajouté',
                    ]);
                }
            }
        }

        $children = new Children();
        $children->setName(strtoupper($data['name']));
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

        return $this->json([
            'success'  => 'ok',
        ]);
    }

    #[Route('/app/admin/adherant/edit', name: 'register_Admin_children_edit')]
    public function registerAdminChildrenEdit(Request $request, EntityManagerInterface $entityManager,UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $user = $userRepository->findOneBy(['id'=>$data['id']]);

        $childrens = $user->getChildrens();

        foreach($childrens as $child){
            if(strtolower($child->getName()) === strtolower($data['name'])){
                if(strtolower($child->getFirstname()) === strtolower($data['firstname'])){
                    return $this->json([
                        'error' => 'Utilisateur déjà ajouté',
                    ]);
                }
            }
        }

        $children = new Children();
        $children->setName(strtoupper($data['name']));
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

        return $this->json([
            'success'  => 'ok',
        ]);
    }

    
    #[Route('/app/adherant/active', name: 'register_Admin_children_active')]
    public function registerAdminChildrenActive(Request $request, EntityManagerInterface $entityManager,ChildrenRepository $childrenRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $children = $childrenRepository->findOneBy(['id'=>$data['id']]);

        $mydate = getDate(strtotime($data['date']));
        $date = new \DateTime();
        date_date_set($date, $mydate['year'], $mydate['mon'], $mydate['mday']);
        date_time_set($date,23,59,59);

        $children->setActiveAt($date);
        $children->setIsActive(true);

        $entityManager->persist($children);
        $entityManager->flush();

        return $this->json([
            'success'  => 'ok',
        ]);
    }

    #[Route('/app/adherant/inactive', name: 'register_Admin_children_desactive')]
    public function registerAdminChildrenDesactive(Request $request, EntityManagerInterface $entityManager,ChildrenRepository $childrenRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $children = $childrenRepository->findOneBy(['id'=>$data['id']]);

        $yesterday = new DateTime('yesterday');

        $children->setActiveAt($yesterday);
        $children->setIsActive(false);

        $entityManager->persist($children);
        $entityManager->flush();

        return $this->json([
            'success'  => 'ok',
        ]);
    }


    #[Route('/app/adherant/delete', name: 'register_Admin_children_Suppr')]
    public function registerAdminChildrenSuppr(Request $request, EntityManagerInterface $entityManager, ChildrenRepository $childrenRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        
        $children = $childrenRepository->findOneBy(['id'=>$data['id']]);
        if($children){
            $entityManager->remove($children);
            $entityManager->flush();
        }else{
            return $this->json([
                'error' => 'Adherant non trouvé',
            ]);
        }

        return $this->json([
            'success'  => "ok",
        ]);
    }

    #[Route('/register', name:'register')]
    public function registerSite(Request $request,
                                UserPasswordHasherInterface $userPasswordHasher,
                                UserRepository $userRepository,
                                UserAuthenticatorInterface $authenticatorManager,
                                LoginAuthenticator $authenticator)
                                {
        $user=new User();
        $form = $this->createForm(RegistrationFormType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setAvatar(rand(1,15).".png");
            $userRepository->save($user, true);

            $request->getSession()->set(Security::LAST_USERNAME, $user->getEmail());

            $this->addFlash('success','Inscription reussi');
            
            $authenticatorManager->authenticateUser($user, $authenticator, $request);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
