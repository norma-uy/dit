<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Vich\Uploadable]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altText = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[
        Vich\UploadableField(
            mapping: 'media.original',
            fileNameProperty: 'originalFileName',
            size: 'size',
            mimeType: 'mimeType',
            originalName: 'originalName',
            dimensions: 'dimensions',
        ),
    ]
    private ?File $originalFile = null;

    #[ORM\Column(length: 255)]
    private ?string $originalFileName = null;

    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(length: 100)]
    private ?string $mimeType = null;

    #[ORM\Column(nullable: true)]
    private ?array $dimensions = [];

    #[ORM\ManyToMany(targetEntity: MediaCollection::class, mappedBy: 'mediaList')]
    private Collection $mediaCollections;

    #[Vich\UploadableField(mapping: 'media.150w', fileNameProperty: 'imageFileName150w')]
    public ?File $imageFile150w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName150w = null;

    #[Vich\UploadableField(mapping: 'media.450w', fileNameProperty: 'imageFileName450w')]
    public ?File $imageFile450w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName450w = null;

    #[Vich\UploadableField(mapping: 'media.800w', fileNameProperty: 'imageFileName800w')]
    public ?File $imageFile800w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName800w = null;

    #[Vich\UploadableField(mapping: 'media.1280w', fileNameProperty: 'imageFileName1280w')]
    public ?File $imageFile1280w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName1280w = null;

    #[Vich\UploadableField(mapping: 'media.1600w', fileNameProperty: 'imageFileName1600w')]
    public ?File $imageFile1600w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName1600w = null;

    #[Vich\UploadableField(mapping: 'media.1920w', fileNameProperty: 'imageFileName1920w')]
    public ?File $imageFile1920w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName1920w = null;

    #[Vich\UploadableField(mapping: 'media.2400w', fileNameProperty: 'imageFileName2400w')]
    public ?File $imageFile2400w = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $imageFileName2400w = null;

    #[ORM\ManyToMany(targetEntity: MediaCategory::class, mappedBy: 'medias')]
    private Collection $mediaCategories;

    public function __construct()
    {
        $this->mediaCollections = new ArrayCollection();
        $this->mediaCategories = new ArrayCollection();
        $this->title = '';
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

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(?string $altText): self
    {
        $this->altText = $altText;

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

    public function getOriginalFile(): ?File
    {
        return $this->originalFile;
    }

    public function setOriginalFile(?File $originalFile = null): self
    {
        $this->originalFile = $originalFile;

        return $this;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(?string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getDimensions(): array
    {
        return $this->dimensions;
    }

    public function setDimensions(?array $dimensions): self
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function __toString(): string
    {
        return !empty($this->title) ? $this->title : (string) $this->originalFileName ?? '';
    }

    public function getMedia(): self
    {
        return $this;
    }

    /**
     * @return Collection<int, MediaCollection>
     */
    public function getMediaCollections(): Collection
    {
        return $this->mediaCollections;
    }

    public function addMediaCollection(MediaCollection $mediaCollection): self
    {
        if (!$this->mediaCollections->contains($mediaCollection)) {
            $this->mediaCollections->add($mediaCollection);
            $mediaCollection->addMedia($this);
        }

        return $this;
    }

    public function removeMediaCollection(MediaCollection $mediaCollection): self
    {
        if ($this->mediaCollections->removeElement($mediaCollection)) {
            $mediaCollection->removeMedia($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, MediaCategory>
     */
    public function getMediaCategories(): Collection
    {
        return $this->mediaCategories;
    }

    public function addMediaCategory(MediaCategory $mediaCategory): self
    {
        if (!$this->mediaCategories->contains($mediaCategory)) {
            $this->mediaCategories->add($mediaCategory);
            $mediaCategory->addMedia($this);
        }

        return $this;
    }

    public function removeMediaCategory(MediaCategory $mediaCategory): self
    {
        if ($this->mediaCategories->removeElement($mediaCategory)) {
            $mediaCategory->removeMedia($this);
        }

        return $this;
    }
}
