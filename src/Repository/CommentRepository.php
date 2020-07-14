<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

     //===== par signalements =====
     /**
      * Récupére les comments par report
      *
      * @return array
      */
     public function getCommentsReport(){
            return $this->getEntityManager()->createQuery(
                'SELECT  c 
                FROM App\Entity\Comment c
                LEFT JOIN c.reports as report
                GROUP BY c.id
                ORDER BY COUNT(report) DESC')
                ->getResult();
    }

    //===== par author =====
    /**
     * récupére les comments par l'auteur dans l'ordre demandé
     *
     * @param string $order
     * @return array
     */
    public function getAllByAuthor($order){
        return $this->getEntityManager()->createQuery(
            'SELECT c
            FROM App\Entity\Comment c
            JOIN c.author a
            GROUP BY c.id
            ORDER BY a.pseudo ' . $order
        )->getResult();
    }


    //===== par date =====
    /**
     * récupére les ads trié par date de création dans l'ordre demandé
     *
     * @param string $order
     * @return array
     */
    public function getAllAdsByDate($order){
        return $this->getEntityManager()->createQuery(
            'SELECT c
            FROM App\Entity\Comment c
            ORDER BY c.createdAt ' . $order)
            ->getResult();
    }


    //===== par post =====
    /**
     * récupére les commentaire par post
     *
     * @param string $order
     * @return array
     */
    public function getAllAdsByPost($order){
        return $this->getEntityManager()->createQuery(
            'SELECT c
            FROM App\Entity\Comment c
            LEFT JOIN c.ad as a
            GROUP BY c
            ORDER BY a.id ' . $order)
            ->getResult();
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
