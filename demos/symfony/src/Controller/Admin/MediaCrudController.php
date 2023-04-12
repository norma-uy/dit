<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Filter\MediaCategoryFilter;
use App\Entity\Media;
use App\Form\Admin\Field\MediaField;
use App\Form\Admin\Field\TextAreaField;
use App\Form\Type\MediaCategoryCollectionType;
use App\Repository\MediaRepository;
use App\Repository\PostRepository;
use App\Service\ImageOptimizer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class MediaCrudController extends AbstractCrudController
{
    /**
     * Undocumented function
     *
     * @param Security $security
     */
    public function __construct(
        private Security $security,
        private LoggerInterface $manualDevLogger,
        private MediaRepository $mediaRepository,
        private PostRepository $postRepository,
        private UploaderHelper $helper,
        private ImageOptimizer $imageOptimizer,
        private FilesystemOperator $storageMediaOriginal,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', 'Archivos')
            ->setEntityLabelInSingular('Archivo')
            ->setEntityLabelInPlural('Archivos')
            ->showEntityActionsInlined()

            // in DETAIL and EDIT pages, the closure receives the current entity
            // as the first argument
            // ->setPageTitle('detail', fn (Product $product) => (string) $product)
            // ->setPageTitle('edit', fn (Category $category) => sprintf('Editing <b>%s</b>', $category->getName()))

            // the help message displayed to end users (it can contain HTML tags)
            // ->setHelp('edit', '...');
            ->setDefaultSort(['id' => 'DESC', 'title' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', 'Título'))
            ->add(MediaCategoryFilter::new('mediaCategories', 'Categorías'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')->hideOnForm(),
            CollectionField::new('mediaCategories', 'Categorías')
                ->setEntryType(MediaCategoryCollectionType::class)
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            MediaField::new('originalFile', 'Archivo')
                ->setRequired(false)
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            TextField::new('title', 'Título')
                ->setRequired(false)
                ->setEmptyData('')
                ->setDefaultColumns('col-md-7 col-xxl-6'),
            TextAreaField::new('altText', 'Texto alternativo')
                ->setDefaultColumns('col-md-7 col-xxl-6')
                ->onlyOnForms(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Crud::PAGE_DETAIL)->add(Crud::PAGE_INDEX, Action::DETAIL, Action::NEW, Action::DELETE);
    }

    public function createEntity(string $entityFqcn)
    {
        $media = new Media();
        $media->setCreatedAt(new DateTimeImmutable('now'));
        return $media;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $currentUser = $this->security->getUser();

        if ($currentUser && $entityInstance instanceof Media) {
            $entityInstance->setAuthor($currentUser);

            $entityManager->persist($entityInstance);
            $entityManager->flush();
        }
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $posts = $this->postRepository->findBy(['thumbnailPhoto' => $entityInstance]);

        foreach ($posts as $key => $post) {
            $post->setThumbnailPhoto(null);
            $entityManager->persist($post);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}
