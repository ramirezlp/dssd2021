<?php

namespace App\Repository;

use App\Entity\SociedadAnonima;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SociedadAnonima|null find($id, $lockMode = null, $lockVersion = null)
 * @method SociedadAnonima|null findOneBy(array $criteria, array $orderBy = null)
 * @method SociedadAnonima[]    findAll()
 * @method SociedadAnonima[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SociedadAnonimaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SociedadAnonima::class);
    }

    // /**
    //  * @return SociedadAnonima[] Returns an array of SociedadAnonima objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SociedadAnonima
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
