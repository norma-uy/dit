<?php

namespace App\Form\Admin\Field\Configurator;

use App\Form\Admin\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

final class TextEditorConfigurator implements FieldConfiguratorInterface
{
    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return TextEditorField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        if (TextEditorField::class === $field->getFieldFqcn()) {
            $field->setFormTypeOptionIfNotSet(
                'attr.rows',
                $field->getCustomOption(TextEditorField::OPTION_NUM_OF_ROWS),
            );
        }
    }
}
