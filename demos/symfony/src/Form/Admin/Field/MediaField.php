<?php

namespace App\Form\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class MediaField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        $package = new Package(new JsonManifestVersionStrategy(getcwd() . '/build/admin/manifest.json'));

        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)

            // this template is used in 'index' and 'detail' pages
            ->setTemplatePath('bundles\EasyAdminBundle\crud\field\media.html.twig')

            // this is used in 'edit' and 'new' pages to edit the field contents
            // you can use your own form types too
            ->setFormType(VichFileType::class, [
                'required' => true,
                'allow_delete' => false,
                'delete_label' => 'vich_uploader.form_label.delete_confirm',
                'download_uri' => true,
                'download_label' => 'vich_uploader.link.download',
                'asset_helper' => true,
                'translation_domain' => 'VichUploaderBundle',
            ])
            ->addCssClass('field-media')
            ->setDefaultColumns('col-md-9 col-xxl-7')

            // loads the CSS and JS assets associated to the given Webpack Encore entry
            // in any CRUD page (index/detail/edit/new). It's equivalent to calling
            // encore_entry_link_tags('...') and encore_entry_script_tags('...')
            // ->addWebpackEncoreEntries('admin-config');

            // these methods allow to define the web assets loaded when the
            // field is displayed in any CRUD page (index/detail/edit/new)
            ->addCssFiles(Asset::new($package->getUrl('build/admin/mediaField.css'))->onlyOnForms())
            ->addJsFiles(Asset::new($package->getUrl('build/admin/mediaField.js'))->onlyOnForms());
    }
}
