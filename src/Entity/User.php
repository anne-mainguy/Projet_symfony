<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields = {"pseudo"},
 * message = "Ce pseudo est déjà utilisé."
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface
{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ obligatoir")
     * @Assert\Regex("/^[0-9a-zA-Zçéèàêâùôäïüëö-]+$/", message="Votre pseudo ne peut contenir que des lettres, nombres et underscore")
     */
    private $pseudo;


    /**
     * Contiendra le mot de passe hashé. Celui qui sera mis dans la bdd
     * @ORM\Column(type="string", length=255)
     * 
     */
    private $hash;

    /**
     * Propriété prévue juste pour récupérer le mot de passe dans le formulaire en lui mettant des restrictions, puis de le hasher en le mettant dans $hash
     * 
     * @Assert\Length(min=8, max=12, minMessage="Le mot de passe doit faire entre 8 et 12 caractères", maxMessage="Le mot de passe doit faire entre 8 et 12 caractères")
     *
     */
    private $pass;

    /**
     * @Assert\EqualTo(propertyPath="pass", message="Saisie incorrect")
     */
    public $confirmPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email(
     * message = "Le format de votre email est incorrect"
     * )
     * 
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ad", mappedBy="author", orphanRemoval=true)
     */
    private $ads;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="author")
     */
    private $votes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Role", mappedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="author")
     */
    private $reports;

    

    public function __construct()
    {
        $this->ads = new ArrayCollection();
        if(empty($this->createdAt)){
            $this->createdAt = new \Datetime();
        }
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }


    /**
     * Attribut un avatar par defaut
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initialize(){
        if(empty($this->avatar)){
            $this->setAvatar('/img/avatar/avatar-icon2.png');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }
    
    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Ad[]
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): self
    {
        if ($this->ads->contains($ad)) {
            $this->ads->removeElement($ad);
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setAuthor($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            if ($vote->getAuthor() === $this) {
                $vote->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRoles(){

        //boucle sur tous les roles attribués a cet utilisateur et fait un tableau du résultat (qui est un objet a la base)
        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();
        })->toArray(); 

        //rajoute le role ROLE_USER a tous les users par deffaut
        $roles[] = 'ROLE_USER';
        return $roles;
    }

    public function getPassword(): ?string
    {
        return $this->hash;
    }

    public function getSalt(){}

    public function getUsername(){
        return $this->pseudo;
    }
    
    public function eraseCredentials(){}

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setAuthor($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            if ($report->getAuthor() === $this) {
                $report->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * compte le nombre de posts fait par l'user
     *
     * @return int
     */
    public function countAd(){
        $nbAds = count($this->getAds());

        return $nbAds;
    }

    /**
     * compte le nombre de commentaires fait par l'user
     *
     * @return int
     */
    public function countComments(){
        return count($this->getComments());
    }

    /**
     * Compte le nombre de signalements fait par l'user
     *
     * @return int
     */
    public function countReports(){
        return count($this->getReports());
    }

    /**
     * Compte le nombre d'annonces de l'user qui ont été signalées
     *
     * @return int
     */
    public function countAdsReported(){
        $ads = $this->getAds();
        $adsReported = 0;
        foreach($ads as $ad){
            if(count($ad->getReports()) > 0){
                $adsReported += 1;
            }
        }
        return $adsReported;
    }

    /**
     * Compte le nombre de commentaires de l'user qui ont été signalés
     *
     * @return int
     */
    public function countCommentsReported(){
        $comments = $this->getComments();
        $commentsReported = 0;
        foreach($comments as $comment){
            if(count($comment->getReports()) > 0){
                $commentsReported += 1;
            }
        }
        return $commentsReported;
    }

}
