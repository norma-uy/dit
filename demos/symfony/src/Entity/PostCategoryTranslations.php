<?php

namespace App\Entity;

use App\Repository\PostCategoryTranslationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostCategoryTranslationsRepository::class)]
class PostCategoryTranslations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $languageCode = null;

    #[ORM\ManyToOne(inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PostCategory $postCategory = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): self
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    public function getPostCategory(): ?PostCategory
    {
        return $this->postCategory;
    }

    public function setPostCategory(?PostCategory $postCategory): self
    {
        $this->postCategory = $postCategory;

        return $this;
    }

    public function getParentEntity(): ?object
    {
        return $this->postCategory;
    }

    public function setParentEntity(?object $entity): self
    {
        $this->postCategory = $entity;

        return $this;
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

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
