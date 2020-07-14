<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceChangeType;
use App\Repository\AdRepository;
use App\Service\AdsDisplayService;
use App\Repository\ThemesRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    
    
    /**
     * Permets de modifier une annonce
     * 
     * @Route("admin/change/ad/{id}", name="admin_ad_change")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @param Request $request
     * 
     * @return Response
     */
    public function changeAd(Ad $ad, ObjectManager $manager, Request $request)
    {
        $form = $this->createForm(AnnonceChangeType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le post a bien été modifié !"
            );

            return $this->redirectToRoute("admin_show_ad", ["id" => $ad->getId()]);
        }

        return $this->render('admin/ad/change.html.twig', [
            'post' => $ad,
            'formChange' => $form->createView()
        ]);
    }



    /**
     * affiche la liste de tous les posts (avec possibilité de modifier l'ordre d'affichage)
     * 
     * @Route("/admin/ads/{tri}/{order}/{themeName}", name="admin_ads_index")
     *
     * @param AdsDisplayService $adsDisplayService
     * @param ThemesRepository $repoThemes
     * @param AdRepository $adRepo
     * @param string $tri
     * @param int $order
     * @param string $themeName
     * 
     * @return Response
     */
    public function index(ThemesRepository $repoThemes, AdRepository $adRepo, $tri, $order = null, $themeName = null)
    {
        ($order == 1) ? $orderString = 'ASC' : $orderString = 'DESC';
        

        switch($tri){
            case 'report':
                $ads = $adRepo->getAllByReport('DESC');
            break;
            case 'author':
                $ads = $adRepo->getAllAdsByAuthor($orderString);
            break;
            case 'theme':
                if($themeName == 'tous'){
                    $ads = $adRepo->getAllAdsByAllTheme($orderString);
                }
                else {
                    $ads = $adRepo->getAllAdsByTheme($themeName, $orderString);
                }
            break;
            case 'date':
                $ads = $adRepo->findBy([], ['createdAt' => $orderString]);

            break;
            case 'comments':
                $ads = $adRepo->getAllAdsByComments($tri, $orderString);
            break;
            case ('likes' || 'dislikes'):
                $ads = $adRepo->getAllAdsByVote($tri, $orderString);
            break;
            default:
            $ads = $adRepo->findBy([], ['id' => $orderString]);
        }

        $themes = $repoThemes->findAll();
        return $this->render('admin/ad/index.html.twig', [
            'posts' => $ads,
            'themes' => $themes,
            'order' => $order,
            'tri' => $tri,
            'nav' => "posts"
        ]);
    }


    /**
     * Supprimer une annonce
     * 
     * @Route("/admin/delete/ad/{id}/{tri}/{order}", name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * 
     * @return Response
     */
    public function delete(Ad $ad, ObjectManager $manager, $tri = 'id', $order = 1){
        $postId = $ad->getId();
        //supprimer le fichier l'image de l'annonce
        $filesystem = new Filesystem();
        $filesystem->remove('.' . $ad->getImage());

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            "success",
            "le post {$postId} de {$ad->getAuthor()->getPseudo()} a bien été supprimé"
        );

        return $this->redirectToRoute("admin_ads_index", ['tri' => $tri, 'order' => $order]);
    }

   
}
