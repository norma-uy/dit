<?php

namespace App\Form\Type;

use App\Entity\Media;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaCollectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Media::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('m')->orderBy('m.id', 'DESC');
            },
            'choice_label' => 'getTitle',
            'expanded' => true,
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
