<?php
namespace App\EventListener;

use App\Entity\PostCategory;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class ControllerListener
{
    public function __construct(private Environment $twig, private EntityManagerInterface $manager)
    {
    }

    public function onKernelController(): void
    {
        $postMenuCategories = $this->manager->getRepository(PostCategory::class)->findBy(['enableMenu' => true]);

        $this->twig->addGlobal('postMenuCategories', $postMenuCategories);
    }
}
