<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class EntityTranslateEasyAdminSubscriber implements EventSubscriberInterface
{
    private AsciiSlugger $slugger;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->slugger = new AsciiSlugger();
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforePersist'],
            BeforeEntityUpdatedEvent::class => ['beforeUpdate'],
            AfterEntityPersistedEvent::class => ['afterPersist'],
            AfterEntityUpdatedEvent::class => ['afterUpdate'],
        ];
    }

    public function beforePersist(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        $this->makeSlug($entity);
    }

    public function beforeUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        $this->makeSlug($entity);
    }

    public function afterPersist(AfterEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        $this->makeTranslations($entity);
    }

    public function afterUpdate(AfterEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        $this->makeTranslations($entity);
    }

    private function makeSlug(object &$entity)
    {
        if (property_exists($entity, 'slug')) {
            if (property_exists($entity, 'title') && !empty($entity->getTitle())) {
                $entity->setSlug($this->slugger->slug($entity->getTitle())->lower());
            } elseif (!empty((string) $entity)) {
                $entity->setSlug($this->slugger->slug((string) $entity)->lower());
            } else {
                $entity->setSlug($this->slugger->slug(uniqid())->lower());
            }
        }
    }

    private function makeTranslations(object &$entity)
    {
        if (property_exists($entity, 'translations')) {
            $locale = $entity->getCurrentLocale();

            $entityTrans = $entity
                ->getTranslations()
                ->filter(function ($gT) use ($locale) {
                    return $gT->getLanguageCode() === $locale;
                })
                ->first();

            $targetEntity = null;
            $reflector = new \ReflectionProperty($entity::class, 'translations');
            foreach ($reflector->getAttributes('Doctrine\\ORM\\Mapping\\OneToMany') as $attribute) {
                $targetEntity = $attribute->getArguments()['targetEntity'];
            }

            if (!$entityTrans && $targetEntity) {
                $entityTrans = new $targetEntity();
            }

            if ($entityTrans) {
                $entityTrans
                    ->setLanguageCode($locale)
                    ->setParentEntity($entity)
                    ->setTitle($entity->getTitle())
                    ->setSlug($entity->getSlug());

                if (property_exists($entityTrans, 'summary') && property_exists($entity, 'summary')) {
                    $entityTrans->setSummary($entity->getSummary());
                }

                if (property_exists($entityTrans, 'content') && property_exists($entity, 'content')) {
                    $entityTrans->setContent($entity->getContent());
                }

                if (property_exists($entityTrans, 'metadata') && property_exists($entity, 'metadata')) {
                    $entityTrans->setMetadata($entity->getMetadata());
                }

                $this->em->persist($entityTrans);
                $this->em->flush();
            }
        }
    }
}
