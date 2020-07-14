<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SumLikesRepository")
 */
class SumLikes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Ad", inversedBy="sumLikes", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $post;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dislikes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Ad
    {
        return $this->post;
    }

    public function setPost(Ad $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getLikes(): ?int
    {
        if($this->likes != null){
            return $this->likes;
        }
        return 0;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        if($this->dislikes != null){
            return $this->dislikes;
        }
        return 0;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function addSumLikes($type){
        $holdSum = $this->$type;
        $newSum = $holdSum + 1;
        $this->$type = $newSum;
    }

    public function removeSumLikes($type){
        $holdSum = $this->$type;
        $newSum = $holdSum - 1;
        $this->$type = $newSum;
    }
}
