<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    
    /**
     * Récupére les posts par report
     *
     * @param string $order
     * @return array
     */
    public function getAllByReport($order){
        return $this->getEntityManager()->createQuery(
            'SELECT  a 
            FROM App\Entity\Ad a
            LEFT JOIN a.reports as report
            GROUP BY a.id
            ORDER BY COUNT(report) ' . $order)
            ->getResult();
    }

    
    /**
     * récupére la liste de ads trié par auteur dans l'ordre demandé (ordre alphabétique DESC ou ASC)
     *
     * @param string $order
     * @return array
     */
    public function getAllAdsByAuthor($order){
        return $this->getEntityManager()->createQuery(
            'SELECT a
            FROM App\Entity\Ad a
            LEFT JOIN a.author as u
            GROUP BY a.id
            ORDER BY u.pseudo ' . $order)
            ->getResult();
    }

     
    /**
     * récuprére la liste des ads par theme et dans l'ordre demandé (ordre alphabétique des themes DESC ou ASC)
     *
     * @param string $order
     * @return array
     */
    public function getAllAdsByAllTheme($order){
       return $this->getEntityManager()->createQuery(
           'SELECT a
           FROM App\Entity\Ad a
           LEFT JOIN a.theme as t
           GROUP BY a.id
           ORDER BY t.theme '.  $order)
           ->getResult();
   }


    /**
     * Récupére toutes les annonces qui correspondent au theme demandé (soit art, soit alimentaire...etc) et dans l'ordre demandé (ordre de l'id des annonce DESC ou ASC).
     *
     * @param string $theme
     * @param string $order
     * @return array
     */
   public function getAllAdsByTheme($theme, $order){
       return $this->getEntityManager()->createQuery(
           'SELECT a
            FROM App\Entity\Ad a
            LEFT JOIN a.theme as t
            WHERE t.theme = :theme
            ORDER BY a.id '.  $order)
            ->setParameter('theme', $theme)
            ->getResult();   
   }

   /**
     * récupére les ads par vote (par like ou dislique) dans le sens demandé
     */
    public function getAllAdsByVote($type, $order){
        return $this->getEntityManager()->createQuery(
            "SELECT a
            FROM App\Entity\Ad a
            LEFT JOIN a.sumLikes as s
            GROUP BY a.id
            ORDER BY s.$type $order")
            ->getResult();
    }


    public function getAllAdsByComments($type, $order){
        return $this->getEntityManager()->createQuery(
            "SELECT a
            FROM App\Entity\Ad a
            LEFT JOIN a.comments as c
            GROUP BY c.ad
            ORDER BY COUNT(c) $order"
        )->getResult();
    }
    
    // /**
    //  * @return Ad[] Returns an array of Ad objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    
}
