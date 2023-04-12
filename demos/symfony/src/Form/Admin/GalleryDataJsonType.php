<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryDataJsonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('rows', CollectionGalleryType::class, [
            'label' => 'galleryData.jsonType.label',
            'label_html' => true,
            'required' => false,
            'entry_type' => GalleryRowJsonType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'allow_order' => true,
            'allow_modal' => true,
            'delete_empty' => true,
            'prefix_name' => 'Fila #',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}

class CollectionGalleryType extends CollectionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['allow_add'] && $options['prototype']) {
            $prototypeOptions = array_replace(
                [
                    'required' => $options['required'],
                    'label' => $options['prototype_name'] . 'label__',
                ],
                array_replace($options['entry_options'], $options['prototype_options']),
            );

            if (null !== $options['prototype_data']) {
                $prototypeOptions['data'] = $options['prototype_data'];
            }

            $prototype = $builder->create($options['prototype_name'], $options['entry_type'], $prototypeOptions);
            $builder->setAttribute('prototype', $prototype->getForm());
        }

        $resizeListener = new ResizeFormListener(
            $options['entry_type'],
            $options['entry_options'],
            $options['allow_add'],
            $options['allow_delete'],
            $options['delete_empty'],
        );

        $builder->addEventSubscriber($resizeListener);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'allow_add' => $options['allow_add'],
            'allow_delete' => $options['allow_delete'],
            'prefix_name' => $options['prefix_name'],
            'allow_order' => $options['allow_order'],
            'allow_modal' => $options['allow_modal'],
        ]);

        if ($form->getConfig()->hasAttribute('prototype')) {
            $prototype = $form->getConfig()->getAttribute('prototype');
            $view->vars['prototype'] = $prototype->setParent($form)->createView($view);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $entryOptionsNormalizer = function (Options $options, $value) {
            $value['block_name'] = 'entry';

            return $value;
        };

        $resolver->setDefaults([
            'allow_add' => false,
            'allow_delete' => false,
            'prefix_name' => '',
            'allow_order' => true,
            'allow_modal' => true,
            'prototype' => true,
            'prototype_data' => null,
            'prototype_name' => '__name__',
            'entry_type' => TextType::class,
            'entry_options' => [],
            'prototype_options' => [],
            'delete_empty' => false,
            'invalid_message' => 'The collection is invalid.',
        ]);

        $resolver->setNormalizer('entry_options', $entryOptionsNormalizer);

        $resolver->setAllowedTypes('delete_empty', ['bool', 'callable']);
        $resolver->setAllowedTypes('prototype_options', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'collection';
    }
}
