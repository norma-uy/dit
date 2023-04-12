<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne]
    private ?Media $thumbnailPhoto = null;

    #[ORM\ManyToOne]
    private ?MediaCollection $mediaSlider = null;

    #[ORM\Column]
    private array $desktopSliderGalleryData = [];

    #[ORM\Column]
    private array $mobileSliderGalleryData = [];

    #[ORM\Column]
    private ?bool $featured = false;

    #[ORM\ManyToMany(targetEntity: PostCategory::class, mappedBy: 'posts')]
    private Collection $postCategories;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostTranslations::class, orphanRemoval: true)]
    private Collection $translations;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $currentLocale = null;

    public function __construct()
    {
        $this->postCategories = new ArrayCollection();
        $this->publishedAt = new DateTimeImmutable('now');
        $this->desktopSliderGalleryData = [];
        $this->mobileSliderGalleryData = [];
        $this->featured = false;
        $this->translations = new ArrayCollection();
        $this->currentLocale = 'es';
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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

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

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

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

    public function getThumbnailPhoto(): ?Media
    {
        return $this->thumbnailPhoto;
    }

    public function setThumbnailPhoto(?Media $thumbnailPhoto): self
    {
        $this->thumbnailPhoto = $thumbnailPhoto;

        return $this;
    }

    public function getMediaSlider(): ?MediaCollection
    {
        return $this->mediaSlider;
    }

    public function setMediaSlider(?MediaCollection $mediaSlider): self
    {
        $this->mediaSlider = $mediaSlider;

        return $this;
    }

    public function getDesktopSliderGalleryData(): array
    {
        return $this->desktopSliderGalleryData;
    }

    public function setDesktopSliderGalleryData(array $desktopSliderGalleryData): self
    {
        $this->desktopSliderGalleryData = $desktopSliderGalleryData;

        return $this;
    }

    public function getMobileSliderGalleryData(): array
    {
        return $this->mobileSliderGalleryData;
    }

    public function setMobileSliderGalleryData(array $mobileSliderGalleryData): self
    {
        $this->mobileSliderGalleryData = $mobileSliderGalleryData;

        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * @return Collection<int, PostCategory>
     */
    public function getPostCategories(): Collection
    {
        return $this->postCategories;
    }

    public function addPostCategory(PostCategory $postCategory): self
    {
        if (!$this->postCategories->contains($postCategory)) {
            $this->postCategories->add($postCategory);
            $postCategory->addPost($this);
        }

        return $this;
    }

    public function removePostCategory(PostCategory $postCategory): self
    {
        if ($this->postCategories->removeElement($postCategory)) {
            $postCategory->removePost($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PostTranslations>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(PostTranslations $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setPost($this);
        }

        return $this;
    }

    public function removePostTranslation(PostTranslations $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getPost() === $this) {
                $translation->setPost(null);
            }
        }

        return $this;
    }

    public function getCurrentLocale(): ?string
    {
        return $this->currentLocale;
    }

    public function setCurrentLocale(?string $currentLocale): self
    {
        $this->currentLocale = $currentLocale;

        return $this;
    }
}
