<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{

    /**
     * Affiche un commentaire en particulier
     * 
     * @Route("/admin/comment/voir/{id}", name="admin_comment_show")
     *
     * @param Comment $comment
     * 
     * @return Response
     */
    public function show(Comment $comment){
        return $this->render("admin/comment/show.html.twig", [
            'comment' => $comment,
            'nav' => 'commentaires'
        ]);
    }

    
    /**
     * Affiche la liste de tt les commentaires (possibilité de modifier l'ordre d'affichage)
     * 
     * @Route("/admin/comment/{tri}/{order}", name="admin_comment_index")
     *
     * @param CommentRepository $commentRepo
     * @param string $tri
     * @param int $order
     * 
     * @return Response
     */
    public function index( CommentRepository $commentRepo, $tri, $order = null)
    {
        ($order == 1) ? $order = 'ASC' : $order = 'DESC';

        switch($tri){
            case 'report':
                $comments = $commentRepo->getCommentsReport();
            break;
            case 'author':
                $comments = $commentRepo->getAllByAuthor($order);
            break;
            case 'date':
                $comments = $commentRepo->getAllAdsByDate($order);
            break;
            case 'post':
                $comments = $commentRepo->getAllAdsByPost($order);
            break;
            default:
            $comments = $commentRepo->findBy([], ['id' => $order]);

        }

        return $this->render('admin/comment/index.html.twig', [
            'tri' => $tri,
            'order' => $order,
            'comments' => $comments,
            'nav' => 'commentaires'
        ]);
    }


    
    /**
     * Supprime un commentaire
     * @Route("/admin/delete/comment/{id}/{tri}/{order}" , name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     */
    public function delete(Comment $comment, ObjectManager $manager, $tri = 'id', $order = 1){
        ($order == 1) ? $order = 'ASC' : $order = 'DESC';
        $commentId = $comment->getId();

        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            "success",
            "le commentaire {$commentId} de {$comment->getAuthor()->getPseudo()} a bien été supprimé"
        );

        return $this->redirectToRoute("admin_comment_index", ['tri' => $tri, 'order' => $order]);

    }
}
