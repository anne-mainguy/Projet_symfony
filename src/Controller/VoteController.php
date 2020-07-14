<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoteController extends AbstractController
{
    /**
     * ajoute un nouveau like/dislike ou modifi le vote
     * 
     * @Route("/liked/{id}/{value}", name="liked_ad")
     *
     * @return JsonResponce
     */
    public function liked(Ad $ad, VoteRepository $voteRepo, ObjectManager $manager, $value)
    {
        switch($value){
            case '1':
                $select = 'likes';
                $noSelect = 'dislikes';
            break;
            case '2':
                $select = 'dislikes';
                $noSelect = 'likes';
            break;
        }

        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'non autorisé'
        ], 403);

        //vérifie si l'user a déja voté et quoi
        //value doit être 1 pour like, 2 pour dislike et false s'il n'a pas encore voté
        $voteValue = $ad->isLikeByUser($user);

        //si il a déja voté like et que la il veut disliker je supprime un like et ajout un dislike
        //si il a déja voté dilike et que la il veut liker je supprime un dislike et ajout un like
        //si il a déja voté like et qu'il re-clique like je supprime juste le like
        //si il a déja voté dislike et qu'il reclicke sur dislike je supprime le dislike

        //si l'utilisateur a déja voté
        if($voteValue){
            //récupére son vote
            $vote = $voteRepo->findOneBy([
                'ad' => $ad,
                'author' => $user
            ]);

            //si le nv vote a la même valeur que l'ancien c'est qu'il veut l'annuler donc on supprime tout
            if($voteValue == $value){
                $manager->remove($vote);
                

                $ad->getSumLikes()->removeSumLikes($select);
                $manager->persist($ad);

                $manager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Like bien supprimé',
                    'likes' => $ad->getSumLikes()->getLikes(),
                    'dislikes' => $ad->getSumLikes()->getDislikes(),
                    'voteValue' => null
                ], 200);

            }
            else {//si il avait voté une valeur et veut la changer
                $vote->setValue($value);//modifie la valeur du vote

                $manager->persist($vote);

                $ad->getSumLikes()->removeSumLikes($noSelect);
                $ad->getSumLikes()->addSumLikes($select);
                $manager->persist($ad);

                $manager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Like bien modifié',
                    'likes' => $ad->getSumLikes()->getLikes(),
                    'dislikes' => $ad->getSumLikes()->getDislikes(),
                    'voteValue' => $value
                ], 200);
            }
        }
        else {//si il n'avait pas encore voté
            $vote = new Vote();

            $vote->setAd($ad)
                ->setAuthor($user)
                ->setValue($value);
            $manager->persist($vote);

            $ad->getSumLikes()->addSumLikes($select);
            $manager->persist($ad);

            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Le vote a été prit en compte',
                'likes' => $ad->getSumLikes()->getLikes(),
                'dislikes' => $ad->getSumLikes()->getDislikes(),
                'voteValue' => $value
            ], 200); 
        }
    }
}
