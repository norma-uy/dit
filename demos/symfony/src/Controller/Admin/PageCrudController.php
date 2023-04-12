<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\PageTranslations;
use App\Form\Admin\AgencyContentDataJsonType;
use App\Form\Admin\ContactContentDataJsonType;
use App\Form\Admin\GalleryRowJsonType;
use App\Form\Admin\HomeContentDataJsonType;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class PageCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @param Security $security
     */
    public function __construct(
        private Security $security,
        private AdminContextProvider $adminContextProvider,
        private PageRepository $pageRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Páginas')
            ->setEntityLabelInSingular('Página')
            ->setEntityLabelInPlural('Páginas')
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

        $package = new Package(new JsonManifestVersionStrategy(getcwd() . '/build/admin/manifest.json'));

        $entityId = $context->getRequest()->query->get('entityId');
        $pageEntity = $entityId ? $this->pageRepository->find($entityId) : null;

        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('title', 'Título')
                ->formatValue(function ($value, Page $entity) use ($locale) {
                    $pageTrans = $entity
                        ->getTranslations()
                        ->filter(function (PageTranslations $trans) use ($locale) {
                            return $trans->getLanguageCode() === $locale;
                        })
                        ->first();

                    return $pageTrans ? $pageTrans->getTitle() : $entity->getTitle();
                })
                ->setDefaultColumns('col-md-7 col-xxl-6')
                ->setFormTypeOption('attr', ['readonly' => 'readonly']),
            ...$pageEntity && in_array($pageEntity->getSlug(), ['agency', 'agencia', 'home', 'inicio'])
                ? [
                    ArrayField::new('desktopSliderGalleryData', 'Slider Desktop')
                        ->setFormType(GalleryRowJsonType::class)
                        ->setRequired(true)
                        ->setDefaultColumns('col-md-7 col-xxl-6')
                        ->hideOnIndex(),
                    ArrayField::new('mobileSliderGalleryData', 'Slider Mobile')
                        ->setFormType(GalleryRowJsonType::class)
                        ->setRequired(false)
                        ->addCssFiles(Asset::new($package->getUrl('build/admin/sliderGalleryField.css'))->onlyOnForms())
                        ->addJsFiles(Asset::new($package->getUrl('build/admin/sliderGalleryField.js'))->onlyOnForms())
                        ->setDefaultColumns('col-md-7 col-xxl-6')
                        ->hideOnIndex(),
                ]
                : [],
            $pageEntity && in_array($pageEntity->getSlug(), ['home', 'inicio'])
                ? ArrayField::new('content', 'Contenido')
                    ->setFormType(HomeContentDataJsonType::class)
                    ->setDefaultColumns('col-md-7 col-xxl-6')
                    ->hideOnIndex()
                : ($pageEntity && in_array($pageEntity->getSlug(), ['agency', 'agencia'])
                    ? ArrayField::new('content', 'Contenido')
                        ->setFormType(AgencyContentDataJsonType::class)
                        ->setDefaultColumns('col-md-9 col-xxl-8')
                        ->hideOnIndex()
                    : ArrayField::new('content', 'Contenido')
                        ->setFormType(ContactContentDataJsonType::class)
                        ->setDefaultColumns('col-md-9 col-xxl-8')
                        ->hideOnIndex()),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        if ($entityInstance instanceof Page) {
            $entityInstance->setCurrentLocale($locale);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        if ($entityInstance instanceof Page) {
            $entityInstance->setCurrentLocale($locale);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Crud::PAGE_DETAIL, Crud::PAGE_NEW, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
