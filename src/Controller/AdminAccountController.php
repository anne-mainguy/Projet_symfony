<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();//renfoie l'erreur si il y en a une et null sinon
        $username = $utils->getLastUsername();//récupére le derneier nom saisie dans le formulaire. Juste pour pouvoir le ré-affichier apres une erreur
        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error != null,
            'username' => $username
        ]);
    }


    /**
     * Déconnexion de l'admin (géré par security.yaml)
     * 
     * @Route("admin/logout", name="admin_account_logout")
     *
     * @return void
     */
    public function logout(){}
}
