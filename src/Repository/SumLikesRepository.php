<?php

namespace App\Repository;

use App\Entity\SumLikes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SumLikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method SumLikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method SumLikes[]    findAll()
 * @method SumLikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SumLikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SumLikes::class);
    }

    // /**
    //  * @return SumLikes[] Returns an array of SumLikes objects
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
    public function findOneBySomeField($value): ?SumLikes
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
