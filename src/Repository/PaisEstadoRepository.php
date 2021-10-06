<?php

namespace App\Repository;

use App\Entity\PaisEstado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaisEstado|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaisEstado|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaisEstado[]    findAll()
 * @method PaisEstado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaisEstadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaisEstado::class);
    }

    // /**
    //  * @return PaisEstado[] Returns an array of PaisEstado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaisEstado
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
