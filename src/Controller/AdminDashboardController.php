<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Service\AdminStatService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(ObjectManager $manager, AdminStatService $adminStatService, AdRepository $adRepo)
    {
        $membres = $adminStatService->getMembres();
        $posts = $adminStatService->getPosts();
        $comments = $adminStatService->getComments();
        $bestPosts = $adminStatService->getBestPosts();
        //renvoie les 3 user qui on le plus de posts
        $bestUsers = $adminStatService->getBestUsers();

        return $this->render('admin/dashboard/index.html.twig', [
            'membres' => $membres,
            'posts' => $posts,
            'comments' => $comments,
            'bestPosts'=> $bestPosts,
            'bestUsers' => $bestUsers,
            'nav' => 'accueil'
        ]);
    }
}
