<?php

namespace App\Entity;

use App\Repository\PostCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostCategoryRepository::class)]
class PostCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Post::class, inversedBy: 'postCategories')]
    private Collection $posts;

    #[ORM\Column]
    private ?bool $enableMenu = false;

    #[ORM\OneToMany(mappedBy: 'postCategory', targetEntity: PostCategoryTranslations::class, orphanRemoval: true)]
    private Collection $translations;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $currentLocale = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->enableMenu = false;
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

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);

        return $this;
    }

    public function __toString(): string
    {
        return $this->title ?: $this->title ?: '';
    }

    public function isEnableMenu(): ?bool
    {
        return $this->enableMenu;
    }

    public function setEnableMenu(bool $enableMenu): self
    {
        $this->enableMenu = $enableMenu;

        return $this;
    }

    /**
     * @return Collection<int, PostCategoryTranslations>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(PostCategoryTranslations $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations->add($translation);
            $translation->setPostCategory($this);
        }

        return $this;
    }

    public function removeTranslation(PostCategoryTranslations $translation): self
    {
        if ($this->translations->removeElement($translation)) {
            // set the owning side to null (unless already changed)
            if ($translation->getPostCategory() === $this) {
                $translation->setPostCategory(null);
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
