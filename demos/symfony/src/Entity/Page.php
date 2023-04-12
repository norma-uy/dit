<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'page', targetEntity: PageTranslations::class, orphanRemoval: true)]
    private Collection $translations;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $currentLocale = null;

    #[ORM\Column(nullable: true)]
    private ?array $content = [];

    #[ORM\Column]
    private array $desktopSliderGalleryData = [];

    #[ORM\Column]
    private array $mobileSliderGalleryData = [];

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->currentLocale = 'es';
        $this->desktopSliderGalleryData = [];
        $this->mobileSliderGalleryData = [];
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

    /**
     * @return Collection<int, PageTranslations>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(PageTranslations $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setPage($this);
        }

        return $this;
    }

    public function removeTranslation(PageTranslations $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getPage() === $this) {
                $translation->setPage(null);
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

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): self
    {
        $this->content = $content;

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
}
