<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryRowJsonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('data', HiddenType::class, [
                'required' => true,
                'empty_data' => '{}',
            ])
            ->add('templateType', HiddenType::class, [
                'required' => true,
                'empty_data' => '',
            ])
            ->add('editGalleryBtn', ButtonType::class, [
                'label' => 'Editar galería',
                'attr' => [
                    'class' => 'edit-gallery-btn',
                ],
            ])
            ->add('order', HiddenType::class, [
                'label' => 'Orden',
                'attr' => ['class' => 'gallery-order'],
                'required' => true,
                'empty_data' => 9999,
            ])
            ->add('id', HiddenType::class, [
                'label' => 'ID',
                'required' => true,
                'empty_data' => 0,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => CollectionType::class,
            'entry_options' => [],
            'allow_add' => false,
            'allow_delete' => false,
            'delete_empty' => false,
        ]);
    }
}
