<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    
      /**
       * Affiche la liste des users (possibilité de modifier l'ordre)
       * 
       * @Route("/admin/user/{tri}/{order?1}", name="admin_user_index")
       *
       * @param UserRepository $userRepo
       * @param string $tri
       * @param int $order
       * @return void
       */
    public function index(UserRepository $userRepo, $tri, $order)
    {
        ($order == 1) ? $orderString = 'ASC' : $orderString = 'DESC';

        if($tri == 'id' || $tri == 'pseudo' || $tri == 'createdAt'){
            $users = $userRepo->findBy([], [$tri => $orderString]);
        }
        else if($tri == 'ads' || $tri == 'comments' || $tri == 'reports'){
            $users = $userRepo->getAllByPropertyEntity($tri, $orderString);
        }
        else{
            $users = $userRepo->findBy([], ['id' => $orderString]);
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'nav' => 'membres',
            'tri' => $tri,
            'order' => $order
        ]);
    }

    
    /**
     * Suppretion des Users
     * 
     * @Route("/admin/delete/user/{id}/{tri}/{order}", name="admin_user_delete")
     *
     * @param User $user
     * @param UserRepository $userRepo
     * @param ObjectManager $manager
     * @param string $tri
     * @param mixt $order
     * @return response
     */
    public function delete(User $user, UserRepository $userRepo, ObjectManager $manager, $tri = 'id', $order = 1){
        ($order == 1) ? $orderString = 'ASC' : $orderString = 'DESC';
       
        if($user->getPseudo() == 'Anonyme'){
            
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas supprimer cet utilisateur, il sert à stocker les posts, commentaires, like et report fait par les utilisateurs supprimés. Il est nécessaire au bon fonctionnement du site.'
            );

            return $this->redirectToRoute('admin_user_index', ['tri' => $tri, 'order' => $order]);
        }
        //cible le user 'anonyme'
        $userAnonyme = $userRepo->findOneBy(['pseudo' => 'anonyme']);

        //fonction qui transfère toutes les données (ad, comment, report, like) de l'user vers l'user anonyme
        function transferDataUserToAnonyme($manager, $dataType, $userAnonyme ,$user){
            $listData = $manager->getRepository($dataType)->findBy(['author' => $user]);
            foreach ($listData as $data) {
                $data->setAuthor($userAnonyme);
                $manager->persist($data);
                $manager->flush();
            }
        }

        //appel la fonction de transfert pour chaque entité a transférer
        transferDataUserToAnonyme($manager, 'App\Entity\Ad', $userAnonyme, $user);
        transferDataUserToAnonyme($manager, 'App\Entity\Comment', $userAnonyme, $user);
        transferDataUserToAnonyme($manager, 'App\Entity\Report', $userAnonyme, $user);
        transferDataUserToAnonyme($manager, 'App\Entity\Vote', $userAnonyme, $user);

        //si l'avatar n'est pas celui par defaut, supprimer le fichier
        if($user->getAvatar() != '/img/avatar/avatar-icon2.png'){
            $pathAvatar = pathinfo($user->getAvatar());
            $nameFile = $pathAvatar['basename'];
            if(file_exists($this->getParameter('avatar_directory') . '/' . $nameFile)){
                unlink($this->getParameter('avatar_directory') . '/' . $nameFile);
            }
        }
        $nameUser = $user->getPseudo();
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            "success",
            "Le compte de {$nameUser} a bien été supprimé"
        );

        return $this->redirectToRoute("admin_user_index", ['tri' => $tri , 'order' => $order]);

    }
}
