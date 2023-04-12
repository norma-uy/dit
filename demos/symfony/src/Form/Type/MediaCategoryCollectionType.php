<?php

namespace App\Form\Type;

use App\Entity\MediaCategory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaCategoryCollectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => MediaCategory::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('mc')->orderBy('mc.title', 'ASC');
            },
            'placeholder' => 'Selecciona una categoría',
            'empty_data' => null,
            'choice_label' => 'title',
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
