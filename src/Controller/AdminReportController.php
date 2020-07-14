<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminReportController extends AbstractController
{

    /**
     * Affiche le détail (tous les Reports qui ont été faits) sur un post
     * 
     * @Route("/admin/report/details/{id}/{type}", name="admin_details_report")
     *
     * 
     * @param ReportRepository $reportRepo
     * @return Response
     */
    public function reportsAd(ObjectManager $manager, ReportRepository $reportRepo, $id, $type){
        if($type == 'post'){
            $target = $manager->find('App\Entity\Ad' , $id);
            $data = $reportRepo->findBy(['ad' => $target]);
        }
        else if($type == 'commentaire'){
            $target = $manager->find('App\Entity\Comment', $id);
            $data = $reportRepo->findBy(['comment' => $target]);
        }

        return $this->render('admin/report/reports.html.twig', [
            'target' => $target,
            'data' => $data,
            'genre' => $type
        ]);
    }
}
