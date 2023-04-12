<?php

namespace App\Form\Admin\Field;

use App\Form\Admin\Type\TextEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Contracts\Translation\TranslatableInterface;

final class TextEditorField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_NUM_OF_ROWS = 'numOfRows';
    public const OPTION_TRIX_EDITOR_CONFIG = 'trixEditorConfig';

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        $package = new Package(new JsonManifestVersionStrategy(getcwd() . '/build/admin/manifest.json'));

        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/text_editor')
            ->setFormType(TextEditorType::class)
            ->addCssClass('field-text_editor')
            ->addCssFiles(Asset::new($package->getUrl('build/admin/textEditorField.css'))->onlyOnForms())
            ->addJsFiles(Asset::new($package->getUrl('build/admin/textEditorField.js'))->onlyOnForms())
            ->setDefaultColumns('col-md-9 col-xxl-7')
            ->setCustomOption(self::OPTION_NUM_OF_ROWS, 10)
            ->setCustomOption(self::OPTION_TRIX_EDITOR_CONFIG, null)
            ->setColumns(10);
    }

    public function setNumOfRows(int $rows): self
    {
        if ($rows < 1) {
            throw new \InvalidArgumentException(
                sprintf('The argument of the "%s()" method must be 1 or higher (%d given).', __METHOD__, $rows),
            );
        }

        $this->setCustomOption(self::OPTION_NUM_OF_ROWS, $rows);

        return $this;
    }

    public function setTrixEditorConfig(array $config): self
    {
        $this->setCustomOption(self::OPTION_TRIX_EDITOR_CONFIG, $config);

        return $this;
    }
}
