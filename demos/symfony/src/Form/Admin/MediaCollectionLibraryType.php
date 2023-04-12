<?php

namespace App\Form\Admin;

use App\Entity\Media;
use App\Entity\MediaCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaCollectionLibraryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'data' => [],
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mediaList', EntityType::class, [
                'class' => Media::class,
                'data' => $options['data'],
                'mapped' => false,
                'multiple' => $options['multiple'],
                'expanded' => true,
            ])
            ->add('templateType', ChoiceType::class, [
                'label' => 'Plantilla',
                'choices' => [
                    'Ancho completo' => 'full-width',
                    '1/2 + 1/2' => '1-2_1-2',
                    '2/3 + 1/3' => '2-3_1-3',
                    '1/3 + 2/3' => '1-3_2-3',
                    '1/3 + 1/3 + 1/3' => '1-3_1-3_1-3',
                ],
            ]);
    }
}
