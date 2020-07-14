<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Vote;
use App\Entity\Themes;
use App\Entity\Comment;
use App\Entity\SumLikes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');

        //============ crée un user pour admin ===============
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $user = new User();
        $passHash = $this->encoder->encodePassword($user, 'password');
        $user->setPseudo('moua')
            ->setHash($passHash)
            ->setEmail('moua@moua.fr')
            ->setCreatedAt($faker->dateTimeThisDecade($max = 'now', $timezone = null))
            ->addUserRole($adminRole);

        $manager->persist($user);
        $manager->flush();

        //=============== crée un user 'anonyme' ===================
        //crée un User dont on va me servir pour stocker les ads, comment, report, like des User qui seront supprimés.
        $anonymeUser = new User();
        $passHash = $this->encoder->encodePassword($user, 'password');
        $anonymeUser->setPseudo('anonymus')
            ->setHash($passHash)
            ->setEmail('moua@moua.fr');

        $manager->persist($user);
        $manager->flush(); 
        
        //=============== crée des Users ==================
        $users = [];
        $genres = ["male", "female"];

        //crée 10 Users
        for($i = 0; $i < 10; $i++){
            $user = new User();
            $genre = $faker->randomElement($genres);

            $pseudo = ($genre == 'male' ? $faker->firstNameMale : $faker->firstNameFemale );

            $avatar = 'https://randomuser.me/api/portraits/';
            $avatarId = $faker->numberBetween(1, 99) . ".jpg";
            $avatar .= ($genre == 'male' ? 'men/' : 'women/') . $avatarId;
            $passHash = $this->encoder->encodePassword($user, 'password');

            $user ->setPseudo($pseudo)
                  ->setHash($passHash)
                  ->setEmail($faker->freeEmail)
                  ->setCreatedAt($faker->dateTimeThisDecade($max = 'now', $timezone = null))
                  ->setAvatar($avatar);

            $manager->persist($user);
            $users[] = $user;
        }

        //============ créer les themes ==============
        $themes = [];
        $themesTab = ["autre", "alimentaire", "apparition", "architecture",  "art", "holala", "nature"];

        for($i = 0; $i < count($themesTab); $i++){
            $theme = new Themes();
            $theme->setTheme($themesTab[$i]);
            $manager->persist($theme);

            $themes[] = $theme;
        }
        //=========== crée des articles ====================
        //crée entre 10 30 articles qui seront attribués aléatoirement aux Users
        for($i = 0; $i < mt_rand(10, 30); $i++){
            $ad = new Ad();
            $user = $users[mt_rand(0, count($users) - 1)];//selectionne un user au hasard
            $theme = $themes[mt_rand(0, count($themes) -1)];//selectionne un theme au hasard

            //============ crée des commentaires ===============
            //ajoute des commentaire pour les annonces
            $comments = [];
            for($j = 0; $j < mt_rand(0, 10); $j++){
                $comment = new Comment();
                $comment->setAd($ad)//correspond a l'annonce en cour de création
                        ->setAuthor($users[mt_rand(0, count($users) - 1)])//user au hasard
                        ->setContent($faker->paragraphs(mt_rand(1,4), true));//de un a 4 paragraphe
                
                $manager->persist($comment);
                $comments[] = $comment;
            }

            //============ crée les likes ====================
            $votes = [];
            $liked = 0;
            $disliked = 0;
            $sumLike = new SumLikes();//pour récupérer le total des likes et des dislikes
            foreach($users as $author){
                $vote = new Vote();
                $value = mt_rand(1,3);

                if($value != 3){
                    if($value == 1){
                        $liked += 1;
                    }else{
                        $disliked += 1;
                    }

                    $vote->setAd($ad)
                        ->setAuthor($author)
                        ->setValue($value);
    
                    $manager->persist($vote);
    
                    $votes[] = $vote;
                }

            }
            $sumLike->setPost($ad)
                    ->setLikes($liked)
                    ->setDislikes($disliked);
            $manager->persist($sumLike);
            

            $ad ->setTitle($faker->words(3, true))
                ->setCreatedAt($faker->dateTimeThisDecade($max = 'now', $timezone = null))
                ->setDescription($faker->sentences(mt_rand(1, 4), true))
                ->setImage($faker->imageUrl(640, 480))
                ->setAuthor($user)
                ->setTheme($theme)
                ->setSumLikes($sumLike)
                ;

            $manager->persist($ad);
            
        }

        $manager->flush();
    }
}
