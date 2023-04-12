<?php

namespace App\Controller\Admin;

use App\Entity\MediaCategory;
use App\Repository\MediaCategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Bundle\SecurityBundle\Security;

class MediaCategoryCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @param Security $security
     */
    public function __construct(private Security $security, private MediaCategoryRepository $mediaCategoryRepository)
    {
    }

    public static function getEntityFqcn(): string
    {
        return MediaCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Categoría')
            ->setEntityLabelInSingular('Categoría')
            ->setEntityLabelInPlural('Categorías')
            ->showEntityActionsInlined()
            ->setDefaultSort(['id' => 'DESC', 'title' => 'ASC']);

        // in DETAIL and EDIT pages, the closure receives the current entity
        // as the first argument
        // ->setPageTitle('detail', fn (Product $product) => (string) $product)
        // ->setPageTitle('edit', fn (Category $category) => sprintf('Editing <b>%s</b>', $category->getName()))

        // the help message displayed to end users (it can contain HTML tags)
        // ->setHelp('edit', '...');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(TextFilter::new('title', 'Título'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('title', 'Título')->setDefaultColumns('col-md-7 col-xxl-6'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_DETAIL)->add(Crud::PAGE_INDEX, Action::DETAIL, Action::NEW, Action::DELETE);
    }
}
