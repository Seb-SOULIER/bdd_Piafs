<?php

namespace App\Service;

use App\Repository\ChildrenRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ActiveUser
{
    private $childrenListRepository;
    private $childrenEntityManager;

    public function __construct(ChildrenRepository $childrenRepository, EntityManagerInterface $entityManager)
    {
        $this->childrenListRepository = $childrenRepository;
        $this->childrenEntityManager = $entityManager;
    }
    
    function inactiveUser()
    {
        $date = new DateTime();

        $childrenListInactif = $this->childrenListRepository->findByDateInf($date);
        foreach($childrenListInactif as $childrenOneInactif){
            $childrenOneInactif->setIsActive(false);
        }

        $childrenListActif = $this->childrenListRepository->findByDateSup($date);
        foreach($childrenListActif as $childrenOneActif){
            $childrenOneActif->setIsActive(true);
        }


        $this->childrenEntityManager->flush();        
    }
}
