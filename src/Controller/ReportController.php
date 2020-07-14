<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Report;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReportController extends AbstractController
{
    
    /**
     * Ajoute un signalement a une Ad
     * 
     * @Route("/report/ad/{id}", name="report_add_ad")
     *
     * @param Ad $ad
     * @return JsonResponce
     */
    public function reportAd(Ad $ad, ObjectManager $manager, Request $request){

        $cause = $request->get('cause');
        $user = $this->getUser();

        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Vous devez être connecté pour pouvoir faire un signalement."
        ],403);

        if($ad->getAuthor() != $user){

            //vérifie si l'user a déjà signalé ce post
            $alreadyRepost = $ad->isReportByUser($user);
    
            if($alreadyRepost){
                $manager->remove($alreadyRepost);
                $manager->flush();

                return $this->json([
                    'code' => 200,
                    'message' => 'Signalement annulé',
                    'add' => 'remove'
                ], 200); 
            }
            else{
                $report = new Report();
                $report->setAd($ad)
                        ->setCause($cause)
                        ->setAuthor($this->getUser());
    
                $manager->persist($report);
                $manager->flush();

    
                return $this->json([
                    'code' => 200,
                    'message' => 'Signalement enregistré',
                    'add' => 'add'
                ], 200);
            }
        }
        else{//l'auteur d'un post ne peut pas signaler son propre post
            return $this->redirectToRoute('show_ad', ['id' => $ad->getId()]);
        }        
    }


    /**
     * Affiche les reports d'un commentaire
     * 
     * @Route("/report/comment/{id}/{cause}", name="report_add_comment")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return response
     */
    public function reportComment(Comment $comment, ObjectManager $manager, $cause = null){
        $user = $this->getUser();

        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Vous devez être connecté pour pouvoir faire un signalement."
        ],403);

        if($comment->getAuthor() != $user){

            //vérifie si l'user a déja signalé ce post
            $alreadyRepost = $comment->isReportByUser($user);
    
            if($alreadyRepost){
                $manager->remove($alreadyRepost);
                $manager->flush();
    
                return $this->json([
                    'code' => 200,
                    'message' => 'Signalement annulé',
                    'add' => 'remove'
                ], 200);
            }
            else{
                $report = new Report();
                $report->setComment($comment)
                        ->setAuthor($this->getUser());
    
                $manager->persist($report);
                $manager->flush();
    
                return $this->json([
                    'code' => 200,
                    'message' => 'Signalement enregistré',
                    'add' => 'add'
                ], 200);
            }
        }
        else{//l'auteur d'un post ne peut pas signaler son propre post
            return $this->json([
                'code' => 403,
                'message' => "Vous ne pouvez pas signaler un commentaire dont vous êtes l'auteur."
            ], 403);
        }
    }
}
