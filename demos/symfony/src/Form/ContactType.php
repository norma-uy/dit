<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nombre',
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'E-mail',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'Teléfono',
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => 'Mensaje',
            ])
            ->add('captcha', ReCaptchaType::class, [
                'mapped' => false,
                'type' => 'checkbox', // (invisible, checkbox)
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
