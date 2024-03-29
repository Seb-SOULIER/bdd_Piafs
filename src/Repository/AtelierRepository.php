<?php

namespace App\Repository;

use App\Entity\Atelier;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Atelier>
 *
 * @method Atelier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Atelier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Atelier[]    findAll()
 * @method Atelier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AtelierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Atelier::class);
    }

    public function save(Atelier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Atelier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Atelier[] Returns an array of Atelier objects
    */

    public function findAllAfter($now): array
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.date >= :val')
           ->setParameter('val', $now)
           ->orderBy('a.hourStart', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }


    /**
    * @return Atelier[] Returns an array of Atelier objects
    */
    public function findByUser(): array
    {
        return $this->createQueryBuilder('a')
        ->andWhere('a.date >= :val')
        ->setParameter('val', new DateTime())
        ->orderBy('a.hourStart', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }

    /**
    * @return Atelier[] Returns an array of Atelier objects
    */
    public function findByAdmin(): array
    {
        return $this->createQueryBuilder('a')
        ->andWhere('a.date >= :val')
        ->setParameter('val', new DateTime())
        ->orderBy('a.hourStart', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }

    /**
    * @return Atelier[] Returns an array of Atelier objects
    */
    public function findByInter($user): array
    {
        return $this->createQueryBuilder('a')
        ->andWhere('a.date >= :val')
        ->andWhere('a.intervenant = :val2')
        ->setParameter('val', new DateTime())
        ->setParameter('val2', $user)
        ->orderBy('a.hourStart', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }

        // return $this->createQueryBuilder('a')
        // //    ->andWhere('a.date => :date')
        // //    ->setParameter('user', $user)
        // //    ->setParameter('date', new DateTime())
        //    ->orderBy('a.date', 'ASC')
        //    ->getQuery()
        //    ->getResult()
        // ;
//    /**
//     * @return Atelier[] Returns an array of Atelier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Atelier
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
