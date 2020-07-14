<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Comment;
use App\Entity\SumLikes;
use App\Form\AnnonceType;
use App\Form\CommentType;
use App\Service\FileService;
use App\Form\AnnonceChangeType;
use App\Repository\AdRepository;
use App\Repository\ThemesRepository;
use App\Repository\CommentRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    /**
     * 
     * @Route("/articles/{name_theme}/{order?DESC}", name="articles")
     * 
     * @return Response
     */
    public function index(AdRepository $repo, ThemesRepository $repoThemes, $name_theme = "tous", $order)
    {
        
        $ads = null;
        $name_valide = false;
        
        if($order != "DESC"){
            $order = "ASC";
        }
 
        
        $themes = $repoThemes->findAll();

        if($name_theme != "tous"){
            foreach( $themes as $item){
                if($item->getTheme() == $name_theme){
                    $name_valide = true;
                    break;
                }
            }
        }
        if($name_theme == "tous"){
            $ads = $repo->findBy([],['id' => $order]);
            $name_valide = true;
        }
        
        if($name_valide == true and $name_theme != 'tous'){
            $ads = $repo->getAllAdsByTheme($name_theme, $order);
        }

        return $this->render('ad/index.html.twig', [
            "articles" => $ads,
            "themes" => $themes,
            "name_theme" => $name_theme,
            "name_valide" => $name_valide,
            "nav" => "posts"
        ]);
    }

    /**
     * Formulaire de création d'annonce
     * 
     * @Route("/article/new", name="ad_create")
     * @return Response
     */
    public function creat(Request $request, ObjectManager $manager, FileService $fileService){
        $ad = new Ad();

        $form = $this->createForm(AnnonceType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            $sumLikes = new SumLikes();
            $sumLikes->setLikes(0);
            $sumLikes->setDislikes(0);

            $ad->setSumLikes($sumLikes);


            //récupére le contenu de mon input file (qui n'est pas relié à ma bdd)
            // $imageFile = $form['imageFile']->getData();
            $imageFile = $form['image']->getData();

            //cette condition est nécessaire parce que le champ « imageFile » n'est pas nécessaire de sorte que le fichier jpg ou autres doit être traité uniquement lorsqu'un fichier est téléchargé
            if($imageFile) {

                $newFilename = $fileService->uploadFile($this->getUser()->getPseudo(), $imageFile  ,$this->getParameter('image_directory'), 'adsImages');

                if($newFilename != null){
                    $ad->setImage($newFilename);
                }
                else{
                    $this->addFlash(
                        "danger",
                        "Fichier inccorect ! Veuillez en selectionner un autre"
                    );

                    return $this->redirectToRoute('ad_create');
                }
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre post a bien été créé ! '
            );

            return $this->redirectToRoute('show_ad', ['id' => $ad->getId()]);
        }

        return $this->render('ad/new.html.twig', [
            'formNew' => $form->createView()
        ]);
    }

    
    /**
     * Modification de l'article par l'auteur
     * 
     * @Route("/article/change/{id}", name="ad_change")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Ce post ne vous appartient pas. Vous ne pouvez pas la modifier.")
     *
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @param Request $request
     * 
     * @return Response
     */
    public function changeAd(Ad $ad, ObjectManager $manager, Request $request){

        if($this->getUser() != $ad->getAuthor()){
            $this->addFlash(
                "danger",
                "Vous devez être l'auteur du post pour pouvoir le modifier"
            );
            return $this->redirectToRoute("show_ad", ["id" => $ad->getId()]);
        }
        $form = $this->createForm(AnnonceChangeType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le post a bien été modifié !"
            );

            return $this->redirectToRoute("show_ad", ["id" => $ad->getId()]);

        }

        return $this->render('ad/change.html.twig', [
            'formChange' => $form->createView()
        ]);

    }


    /**
     * Supprimer unarticle
     * 
     * @Route("/article/delete/{id}", name="ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * 
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager){
        $user = $this->getUser();
        
        if($user != $ad->getAuthor()){
            $this->addFlash(
                "danger",
                "Vous ne pouvez pas supprimer un post qui ne vous appartient pas !"
            );

            return $this->redirectToRoute("show_ad", ['id' => $ad->getId()]);
        }
        else{
            //supprimer le fichier image de l'article
            $filesystem = new Filesystem();
            $filesystem->remove('.' . $ad->getImage());
    
            $manager->remove($ad);
            $manager->flush();
    
            $this->addFlash(
                "success",
                "Votre post a bien été supprimé"
            );
    
            return $this->redirectToRoute("user_show", ['pseudo' => $user->getPseudo()]);

        }
    }


    
    /**
     * envoie vers la page d'un article en particulier
     * 
     * @Route("/article/{id}", name="show_ad")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @param Request $req
     * @param CommentRepository $commentRepo
     * 
     * @return Response
     */
    public function show(Ad $ad, ObjectManager $manager , Request $req, CommentRepository $commentRepo){

        $user = $this->getUser();
        $reportUser = false;
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($req);

        if($user){//si l'utilisateur est connecté
            if($formComment->isSubmitted() && $formComment->isValid()){
                $comment->setAd($ad)
                        ->setAuthor($user);
    
                $manager->persist($comment);
                $manager->flush();
    
                //vide le formulaire
                //en redirigeant vers le mme page
                return $this->redirect($req->getUri());
            }

            $reportUser = $ad->isReportByUser($user);
        }
        
        //récupére les commentaires par ordre, du plus resent au plus vieux
        $comments = $commentRepo->findBy(['ad' => $ad], ['id' => 'DESC']);
        
        return $this->render('ad/show.html.twig', [
            "formComment" => $formComment->createView(),
            "comments" => $comments,
            "article" => $ad,
            'likes' => $ad->getSumLikes()->getLikes(),
            'dislikes' => $ad->getSumLikes()->getDislikes() ,
            'report' => $reportUser
        ]);
    }


}
