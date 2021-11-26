<?php

namespace App\Entity;

use App\Repository\TrickVideoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrickVideoRepository::class)
 */
class TrickVideo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $embed_code;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmbedCode(): ?string
    {
        return $this->embed_code;
    }

    public function setEmbedCode(string $embed_code): self
    {
        $this->embed_code = $embed_code;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
