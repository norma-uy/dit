<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HomeContentDataJsonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('bannerTitle', TextareaType::class, [
                'label' => 'Título del Banner',
                'attr' => ['rows' => '5'],
                'help' => "
                <ul>
                    <li>Use el prefijo, <code>mailto:</code> para crear enlace a correo electrónico.</li>
                </ul>",
                'required' => false,
            ])
            ->add('bannerSubTitle', TextareaType::class, [
                'label' => 'Subtítulo del Banner',
                'attr' => ['rows' => '5'],
                'help' => "
                <ul>
                    <li>Use el prefijo, <code>mailto:</code> para crear enlace a correo electrónico.</li>
                </ul>",
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
