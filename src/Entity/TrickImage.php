<?php

namespace App\Entity;

use App\Repository\TrickImageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrickImageRepository::class)
 */
class TrickImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="trickImages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_thumbnail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        if(!$this->filename)
            $this->setFilename('trick-default.png');
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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

    public function getIsThumbnail(): ?bool
    {
        return $this->is_thumbnail;
    }

    public function setIsThumbnail(bool $is_thumbnail): self
    {
        $this->is_thumbnail = $is_thumbnail;

        return $this;
    }
}
