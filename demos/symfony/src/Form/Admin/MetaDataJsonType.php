<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetaDataJsonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('height', TextType::class, [
                'label' => 'Altura',
                'required' => false,
            ])
            ->add('bustSize', TextType::class, [
                'label' => 'Busto',
                'required' => false,
            ])
            ->add('chest', TextType::class, [
                'label' => 'Pecho',
                'required' => false,
            ])
            ->add('waistSize', TextType::class, [
                'label' => 'Cintura',
                'required' => false,
            ])
            ->add('hipSize', TextType::class, [
                'label' => 'Cadera',
                'required' => false,
            ])
            ->add('shoeSize', TextType::class, [
                'label' => 'Calzado',
                'required' => false,
            ])
            ->add('coloredEyes', TextType::class, [
                'label' => 'Ojos',
                'required' => false,
            ])
            ->add('hair', TextType::class, [
                'label' => 'Cabello',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => false,
            'allow_delete' => false,
            'delete_empty' => false,
            'entry_options' => null,
            'entry_type' => null,
        ]);
    }
}
