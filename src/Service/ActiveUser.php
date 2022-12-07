<?php

namespace App\Service;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ActiveUser
{
    private $userListRepository;
    private $userEntityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userListRepository = $userRepository;
        $this->userEntityManager = $entityManager;
    }
    
    function inactiveUser()
    {
        $userList = $this->userListRepository->findAll();
        
        foreach($userList as $userOne){
            if($userOne->getActiveAt()){
                if($userOne->getActiveAt() > new DateTime()){
                    $userOne->setIsActive(true);
                }else{
                    $userOne->setIsActive(false);
                }
            }else{
                if($userOne->isIsActive() === true){
                    $userOne->setIsActive(false);
                }
            }
        $this->userEntityManager->persist($userOne);
        $this->userEntityManager->flush();        
        }
    }
}
