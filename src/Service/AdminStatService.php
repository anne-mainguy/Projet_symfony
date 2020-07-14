<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;


class AdminStatService {
     
    private $manager;


    public function __construct(ObjectManager $manager){
        $this->manager = $manager;
    }

    public function getMembres(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getPosts(){
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getComments(){
        return $this->manager->getRepository('App\Entity\Comment')->findAll();
    }

    public function getBestPosts(){
        return $this->manager->createQuery(
            'SELECT a 
            FROM App\Entity\Ad a
            JOIN a.sumLikes l
            WHERE l.likes > l.dislikes
            ORDER BY l.likes DESC
            ')->setMaxResults(3)->getResult();
    }

    public function getBestUsers(){
        return $this->manager->createQuery(
            'SELECT u
             FROM App\Entity\User u 
             LEFT JOIN u.ads as posts
             GROUP BY u.id
             ORDER BY COUNT(posts) DESC')
             ->setMaxResults(3)
            ->getResult();
    }


}