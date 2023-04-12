<?php

namespace App\Controller\Admin;

use App\Entity\PostCategory;
use App\Entity\PostCategoryTranslations;
use App\Repository\PostCategoryRepository;
use App\Repository\PostCategoryTranslationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Bundle\SecurityBundle\Security;

class PostCategoryCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @param Security $security
     */
    public function __construct(
        private Security $security,
        private PostCategoryRepository $postCategoryRepository,
        private AdminContextProvider $adminContextProvider,
        private PostCategoryTranslationsRepository $postCategoryTranslationsRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return PostCategory::class;
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
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('title', 'Título')
                ->formatValue(function ($value, PostCategory $entity) use ($locale) {
                    $postCategoryTrans = $entity
                        ->getTranslations()
                        ->filter(function (PostCategoryTranslations $trans) use ($locale) {
                            return $trans->getLanguageCode() === $locale;
                        })
                        ->first();

                    return $postCategoryTrans ? $postCategoryTrans->getTitle() : $entity->getTitle();
                })
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            // BooleanField::new('enableMenu', 'Habilitar en el menú')->renderAsSwitch(false),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_DETAIL)->add(Crud::PAGE_INDEX, Action::DETAIL, Action::NEW, Action::DELETE);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        if ($entityInstance instanceof PostCategory) {
            $entityInstance->setCurrentLocale($locale);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        if ($entityInstance instanceof PostCategory) {
            $entityInstance->setCurrentLocale($locale);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }
}
