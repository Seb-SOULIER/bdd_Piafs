<?php

namespace App\Repository;

use App\Entity\Children;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Children>
 *
 * @method Children|null find($id, $lockMode = null, $lockVersion = null)
 * @method Children|null findOneBy(array $criteria, array $orderBy = null)
 * @method Children[]    findAll()
 * @method Children[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChildrenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Children::class);
    }

    public function save(Children $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Children $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return Children[] Returns an array of Children objects
    */
   public function findByDateInf($date): array
   {
        return $this->createQueryBuilder('c')
           ->andWhere('c.activeAt < :val')
           ->OrWhere('c.activeAt IS NULL')
           ->setParameter('val', $date)
           ->getQuery()
           ->getResult()
       ;
   }

    /**
    * @return Children[] Returns an array of Children objects
    */
    public function findByDateSup($date): array
    {
         return $this->createQueryBuilder('c')
            ->andWhere('c.activeAt >= :val')
            ->setParameter('val', $date)
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Children[] Returns an array of Children objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Children
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
