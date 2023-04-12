<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\PostCategoryFilter;
use App\Entity\Post;
use App\Entity\PostTranslations;
use App\Form\Admin\Field\TextAreaField;
use App\Form\Admin\Field\TextEditorField;
use App\Form\Admin\GalleryRowJsonType;
use App\Form\Type\CategoriesCollectionType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

class PostCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @param Security $security
     */
    public function __construct(private Security $security, private AdminContextProvider $adminContextProvider)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Noticias')
            ->setEntityLabelInSingular('Noticia')
            ->setEntityLabelInPlural('Noticias')
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
        return $filters
            ->add(TextFilter::new('title', 'Título'))
            ->add(PostCategoryFilter::new('postCategories', 'Categorías'));
    }

    public function configureFields(string $pageName): iterable
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        $package = new Package(new JsonManifestVersionStrategy(getcwd() . '/build/admin/manifest.json'));

        return [
            IdField::new('id', 'ID')->hideOnForm(),
            TextField::new('title', 'Título')
                ->formatValue(function ($value, Post $entity) use ($locale) {
                    $postCategoryTrans = $entity
                        ->getTranslations()
                        ->filter(function (PostTranslations $trans) use ($locale) {
                            return $trans->getLanguageCode() === $locale;
                        })
                        ->first();

                    return $postCategoryTrans ? $postCategoryTrans->getTitle() : $entity->getTitle();
                })
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            AssociationField::new('thumbnailPhoto', 'Foto miniatura')
                ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                    $queryBuilder
                        ->join('entity.mediaCategories', 'mc')
                        ->andWhere($queryBuilder->expr()->eq('mc.slug', ':slug'))
                        ->orderBy('entity.id', 'DESC')
                        ->setParameter(':slug', 'miniatura');
                })
                ->renderAsNativeWidget(false)
                ->autocomplete()
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            BooleanField::new('featured', 'Destacado')->renderAsSwitch(false),
            CollectionField::new('postCategories', 'Categorías')
                ->setEntryType(CategoriesCollectionType::class)
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            DateField::new('publishedAt', 'Fecha de publicación')->setDefaultColumns('col-md-7 col-xxl-6'),
            // AssociationField::new('mediaSlider', 'Slider de fotos')->hideOnIndex(),
            ArrayField::new('desktopSliderGalleryData', 'Slider Desktop')
                ->setFormType(GalleryRowJsonType::class)
                ->setDefaultColumns('col-md-7 col-xxl-6')
                ->hideOnIndex(),
            ArrayField::new('mobileSliderGalleryData', 'Slider Mobile')
                ->setFormType(GalleryRowJsonType::class)
                ->setRequired(false)
                ->addCssFiles(Asset::new($package->getUrl('build/admin/sliderGalleryField.css'))->onlyOnForms())
                ->addJsFiles(Asset::new($package->getUrl('build/admin/sliderGalleryField.js'))->onlyOnForms())
                ->setDefaultColumns('col-md-7 col-xxl-6')
                ->hideOnIndex(),
            TextAreaField::new('summary', 'Resumen')
                ->formatValue(function ($value, Post $entity) use ($locale) {
                    $postTrans = $entity
                        ->getTranslations()
                        ->filter(function (PostTranslations $trans) use ($locale) {
                            return $trans->getLanguageCode() === $locale;
                        })
                        ->first();

                    return $postTrans ? $postTrans->getSummary() : $entity->getSummary();
                })
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            TextEditorField::new('content', 'Contenido')
                ->formatValue(function ($value, Post $entity) use ($locale) {
                    $postTrans = $entity
                        ->getTranslations()
                        ->filter(function (PostTranslations $trans) use ($locale) {
                            return $trans->getLanguageCode() === $locale;
                        })
                        ->first();

                    return $postTrans ? $postTrans->getContent() : $entity->getContent();
                })
                ->setDefaultColumns('col-md-7 col-xxl-6')
                ->hideOnIndex(),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $post = new Post();
        $post->setCreatedAt(new DateTimeImmutable('now'));
        return $post;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        $currentUser = $this->security->getUser();

        if ($currentUser && $entityInstance instanceof Post) {
            $entityInstance->setAuthor($currentUser)->setCurrentLocale($locale);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $context = $this->adminContextProvider->getContext();
        $locale = $context->getI18n()->getLocale();

        if ($entityInstance instanceof Post) {
            $entityInstance->setCurrentLocale($locale);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_DETAIL)->add(Crud::PAGE_INDEX, Action::DETAIL, Action::NEW, Action::DELETE);
    }
}
