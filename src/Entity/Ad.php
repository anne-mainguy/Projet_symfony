<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\AdRepository")
 * @ORM\HasLifecycleCallbacks
 * 
 */
class Ad
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Champ obligatoir")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Themes", inversedBy="ads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $theme;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="ad", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="ad", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SumLikes", mappedBy="post", cascade={"persist", "remove"})
     */
    private $sumLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="ad", orphanRemoval=true)
     */
    private $reports;

    

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }


    /**
     * Injecte la date avant de persister et d'updater si elle n'est pas déja présente
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeCreatedAt(){
        if(empty($this->createdAt)){
            $date = new \DateTime();
            $this->createdAt = $date;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = htmlspecialchars($title);

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = htmlspecialchars($description);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTheme(): ?Themes
    {
        return $this->theme;
    }

    public function setTheme(?Themes $theme): self
    {
        $this->theme = $theme;

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
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
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
            $vote->setAd($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            if ($vote->getAd() === $this) {
                $vote->setAd(null);
            }
        }

        return $this;
    }


    /**
     * Recherche si l'utilisateur connecté a déja fait un vote sur l'annonce. Si oui ça retourne la valeur 1 pour like, 2 pour dislike et false si il n'a pas voté
     *
     * @param User $user
     * @return boolean
     */
    public function isLikeByUser(User $user){
        foreach($this->votes as $vote){
            if($vote->getAuthor() == $user){
                return $vote->getValue();
            }
        }
        return false;
    }

    public function getSumLikes(): ?SumLikes
    {
        return $this->sumLikes;
    }

    public function setSumLikes(SumLikes $sumLikes): self
    {
        $this->sumLikes = $sumLikes;

        if ($this !== $sumLikes->getPost()) {
            $sumLikes->setPost($this);
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
            $report->setAd($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            if ($report->getAd() === $this) {
                $report->setAd(null);
            }
        }

        return $this;
    }

    /**
     * Renvoie true si l'utilisateur a déja fait un signalement pour ce post et false sinon
     *
     * @param User $user
     * @return boolean
     */
    public function isReportByUser(User $user){
        foreach($this->reports as $report){
            if($report->getAuthor() == $user){
                return $report;
            }
        }
        return false;
    }

    public function countComments(){
        return count($this->comments);
    }

}
