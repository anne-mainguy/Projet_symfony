<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Service\FileService;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Gère et affiche le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error != null,
            'username' => $username
        ]);
    }

    /**
     * Gère la déconnexion
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){}


    /**
     * Gère le formulaire d'inscription
     * 
     * @Route("register", name="account_register")
     *
     * @param Request $requet
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encode
     * @param FileService $fileService
     * @return Response
     */
    public function register(Request $requet, ObjectManager $manager, UserPasswordEncoderInterface $encode, FileService $fileService){
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($requet);

        if($form->isSubmitted() && $form->isValid()){

            //hash le password
            $hash = $encode->encodePassword($user, $user->getPass());

            //récupère le contenu de l'input file (qui n'est pas relié a ma bdd)
            $avatarFile = $form['avatarFile']->getData();

            //cette condition est nécessaire parce que le champ « imageFile » n'est pas obligatoire de sorte que le fichier jpg ou autres (png, pdf...) doit être traité uniquement lorsqu'un fichier est téléchargé
            if($avatarFile) {
                
                $newFilename = $fileService->uploadFile($form['pseudo']->getData(), $avatarFile , $this->getParameter('avatar_directory') , 'avatar');
                // avatar_directory fait référence à un parameter dans config/service.yaml qui indique l'endroit ou doit être enregistré l'image une fois téléchargée (soit ex : /public/img/adsImages) utilisé grace a AbstractController

                if($newFilename != null){
                    $user->setAvatar($newFilename);
                }
                else{
                    $this->addFlash(
                        "danger",
                        "Fichier inccorect ! Veuillez en selectionner un autre"
                    );

                    return $this->redirectToRoute('account_register');
                }
            }

            $user->setHash($hash);
            
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé !'
            );

            return $this->redirectToRoute("account_login");

        }

        return $this->render("account/register.html.twig", [
            "formInsc" => $form->createView()
        ]);

    }

    /**
     * Modification du profile
     * 
     * @Route("/account/profile", name="account_profile")
     *
     * @param Request $request
     * @param ObjectManager $manager
     * @param FileService $fileService
     * @return Response
     */
    public function profile(Request $request, ObjectManager $manager, FileService $fileService){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            //si le contenu de l'input n'est pas vide et est différent de l'url qui est déja dans la bdd c'est que l'avatar demandé est celui de base
            //donc même dans le cas ou l'on met un url 'de force' au pire on aura juste l'avatar de base
            
            if($form['controlleAvatar']->getData() == 'new avatar'){

                //récupère le contenu de mon input file (qui n'est pas relié a ma bdd)
                $avatarFile = $form['avatarFile']->getData();
    
                //cette condition est nécessaire parce que le champ « imageFile » n'est pas nécessaire de sorte que le fichier jpg ou autres doit être traité uniquement lorsqu'un fichier est téléchargé
                if($avatarFile) {

                    $newFilename = $fileService->uploadFile($form['pseudo']->getData(), $avatarFile, $this->getParameter('avatar_directory'), 'avatar' , $user->getAvatar());
                    if($newFilename != false){
                        $user->setAvatar($newFilename);
                    }
                    else{
                        $this->addFlash(
                            "danger",
                            "Fichier inccorect ! Aucune modification n'a été pris en compte"
                        );

                        return $this->redirectToRoute('account_profile');
                    }
                }
            }
            else if($form['controlleAvatar']->getData() != $user->getAvatar() and $form['controlleAvatar']->getData() != '') {
                $user->setAvatar('/img/avatar/avatar-icon2.png');
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                "success",
                "Les modifications ont bien été prises en compte"
            );

            return $this->redirectToRoute("user_show", ['pseudo'=> $user->getPseudo()]);
        }
        
        return $this->render("account/profile.html.twig", [
            "formChange" => $form->createView(),
            "image" => $user->getAvatar()
        ]);
    }


    /**
     * Formulaire particulier pour modifier le mot de passe
     *
     * @Route("/account/password-update", name="account_password")
     *
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $passwordEncoder ){
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            //si la saisie de l'ancien mot de passe n'est pas le bonne
            if(!$passwordEncoder->isPasswordValid($user, $form->get('oldPassword')->getData())){
                //ajoute une erreur au champ 'oldPassword' avec un message
                // $form->get('oldPasword)  récupère la saisie du champ 'oldPassword'
                //->addError(new FormError("Mot de passe incorrecte.") ajoute un message d'erreur 
                $form->get('oldPassword')->addError(new FormError("Mot de passe incorrecte."));
            }
            else {//si le mot de passe est bon

                $newPass = $form->get('plainPassword')->getData();
                if(strlen($newPass) < 8 || strlen($newPass) > 12){
                    $form['plainPassword']['first']->addError(new FormError('Entre 8 et 12 caractères'));
                }
                else{
                    $user->setHash($passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()));
    
                    $manager->persist($user);
                    $manager->flush();
    
                    $this->addFlash(
                        'success',
                        'Votre mot de passe a bien été modifié'
                    );
                }

            }
        }

        return $this->render('account/password.html.twig', [
            'formPass' => $form->createView()
        ]);
    }


    /**
     * Affiche la page du profil personnalisé de l'utilisateur connecté d'ou il peut le modifier
     *
     * @Route("/account", name="account_index")
     * @return Response
     */
    public function myAccount(){
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }
}
