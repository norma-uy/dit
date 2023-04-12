<?php

namespace App\Entity;

use App\Repository\MediaCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaCollectionRepository::class)]
class MediaCollection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $linkTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'mediaCollections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Media::class, inversedBy: 'mediaCollections')]
    public Collection $mediaList;

    #[ORM\Column]
    private bool $setAsHomeSlider = false;

    public function __construct()
    {
        $this->mediaList = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLinkTo(): ?string
    {
        return $this->linkTo;
    }

    public function setLinkTo(?string $linkTo): self
    {
        $this->linkTo = $linkTo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

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
     * @return Collection<int, Media>
     */
    public function getMediaList(): Collection
    {
        return $this->mediaList;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->mediaList->contains($media)) {
            $this->mediaList->add($media);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        $this->mediaList->removeElement($media);

        return $this;
    }

    public function __toString(): string
    {
        return $this->title ?? '';
    }

    public function isSetAsHomeSlider(): ?bool
    {
        return $this->setAsHomeSlider;
    }

    public function setAsHomeSlider(bool $setAsHomeSlider): self
    {
        $this->setAsHomeSlider = $setAsHomeSlider;

        return $this;
    }
}
