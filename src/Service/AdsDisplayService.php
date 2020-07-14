<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;


class AdsDisplayService {


    private $manager;

    public function __construct(ObjectManager $manager){
        $this->manager = $manager;
    }

    /**
     * Récupére le nombre de likes ou de dislikes attribués a une annonce (like = 1 dislike = 2)
     *
     * @param object $ad
     * @param int $value
     * @return int
     */
    public function getLikesOrDislikes($ad, $value){
        return $this->manager->createQuery(
            'SELECT count(l) 
            FROM App\Entity\Vote l 
            WHERE l.ad = :ad AND l.value = :value'
            )->setParameter('ad', $ad)
            ->setParameter('value', $value)
            ->getSingleScalarResult();
    }

    /**
     * récupére les commentaires d'une annonce dans le sens inverse (du plus récent au plus vieux)
     */
    public function getCommentReverse($ad){
        return $this->manager->createQuery(
            'SELECT c FROM App\Entity\Comment c 
            WHERE c.ad = :ad 
            ORDER BY DESC')
            ->setParameter('ad', $ad)
            ->getResult();
    }
    
}