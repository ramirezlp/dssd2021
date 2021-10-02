<?php

namespace App\Repository;

use App\Entity\SociedadAnonimaSocio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SociedadAnonimaSocio|null find($id, $lockMode = null, $lockVersion = null)
 * @method SociedadAnonimaSocio|null findOneBy(array $criteria, array $orderBy = null)
 * @method SociedadAnonimaSocio[]    findAll()
 * @method SociedadAnonimaSocio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SociedadAnonimaSocioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SociedadAnonimaSocio::class);
    }

    // /**
    //  * @return SociedadAnonimaSocio[] Returns an array of SociedadAnonimaSocio objects
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
    public function findOneBySomeField($value): ?SociedadAnonimaSocio
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
