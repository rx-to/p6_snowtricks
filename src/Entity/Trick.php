<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 */
class Trick
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $creation_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_date;

    /**
     * @ORM\ManyToOne(targetEntity=TrickCategory::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=TrickImage::class, mappedBy="trick", orphanRemoval=true)
     * @MaxDepth(2) 
     */
    private $trickImages;

    /**
     * @ORM\OneToMany(targetEntity=TrickVideo::class, mappedBy="trick", orphanRemoval=true)
     * @MaxDepth(2) 
     */
    private $trickVideos;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $is_draft;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=TrickMessage::class, mappedBy="trick", orphanRemoval=true)
     */
    private $trickMessages;

    public function __construct()
    {
        $this->trickImages = new ArrayCollection();
        $this->trickVideos = new ArrayCollection();
        $this->trickMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(?\DateTimeInterface $update_date): self
    {
        $this->update_date = $update_date;

        return $this;
    }

    public function getCategory(): ?TrickCategory
    {
        return $this->category;
    }

    public function setCategory(?TrickCategory $category): self
    {
        $this->category = $category;

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

    /**
     * @return Collection|TrickImage[]
     */
    public function getTrickImages(): Collection
    {
        return $this->trickImages;
    }

    public function addTrickImage(TrickImage $trickImage): self
    {
        if (!$this->trickImages->contains($trickImage)) {
            $this->trickImages[] = $trickImage;
            $trickImage->setTrick($this);
        }

        return $this;
    }

    public function removeTrickImage(TrickImage $trickImage): self
    {
        if ($this->trickImages->removeElement($trickImage)) {
            // set the owning side to null (unless already changed)
            if ($trickImage->getTrick() === $this) {
                $trickImage->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrickVideo[]
     */
    public function getTrickVideos(): Collection
    {
        return $this->trickVideos;
    }

    public function addTrickVideo(TrickVideo $trickVideo): self
    {
        if (!$this->trickVideos->contains($trickVideo)) {
            $this->trickVideos[] = $trickVideo;
            $trickVideo->setTrick($this);
        }

        return $this;
    }

    public function removeTrickVideo(TrickVideo $trickVideo): self
    {
        if ($this->trickVideos->removeElement($trickVideo)) {
            // set the owning side to null (unless already changed)
            if ($trickVideo->getTrick() === $this) {
                $trickVideo->setTrick(null);
            }
        }

        return $this;
    }

    public function getIsDraft(): ?bool
    {
        return $this->is_draft;
    }

    public function setIsDraft(bool $is_draft): self
    {
        $this->is_draft = $is_draft;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|TrickMessage[]
     */
    public function getTrickMessages(): Collection
    {
        return $this->trickMessages;
    }

    public function addTrickMessage(TrickMessage $trickMessage): self
    {
        if (!$this->trickMessages->contains($trickMessage)) {
            $this->trickMessages[] = $trickMessage;
            $trickMessage->setTrick($this);
        }

        return $this;
    }

    public function removeTrickMessage(TrickMessage $trickMessage): self
    {
        if ($this->trickMessages->removeElement($trickMessage)) {
            // set the owning side to null (unless already changed)
            if ($trickMessage->getTrick() === $this) {
                $trickMessage->setTrick(null);
            }
        }

        return $this;
    }
}
