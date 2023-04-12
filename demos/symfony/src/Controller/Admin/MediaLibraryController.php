<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\Page;
use App\Entity\Post;
use App\Form\Admin\MediaCollectionLibraryType;
use App\Form\Admin\UploadMediaLibraryType;
use App\Repository\MediaCategoryRepository;
use App\Repository\MediaRepository;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/api')]
class MediaLibraryController extends AbstractController
{
    public function __construct(
        private MediaRepository $mediaRepository,
        private MediaCategoryRepository $mediaCategoryRepository,
        private PageRepository $pageRepository,
        private PostRepository $postRepository,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/simple-media-library/{page}', name: 'simple_media_library', requirements: ['page' => '\d+'])]
    public function simple(Request $request, int $page = 1): JsonResponse
    {
        $media = new Media();

        $uploadMediaUploadForm = $this->createForm(UploadMediaLibraryType::class, $media);
        $simpleMediaChoiceForm = $this->createForm(MediaCollectionLibraryType::class, null);

        $mediaTemplateContent = $this->renderView('admin/mediaLibrary/simple_media.html.twig', [
            'title' => 'Media Library',
            'uploadMediaUploadForm' => $uploadMediaUploadForm,
            'simpleMediaChoiceForm' => $simpleMediaChoiceForm,
        ]);

        return new JsonResponse([
            'forms' => [
                'libraryHtmlTab' => $mediaTemplateContent,
            ],
        ]);
    }

    #[Route('/gallery-media', name: 'gallery_media')]
    public function gallery(Request $request): JsonResponse
    {
        $mediaListPerPages = $this->mediaRepository->findPerPage(1);
        $mediaCategories = $this->mediaCategoryRepository->findAll();

        $enableTextFields = $request->query->get('enableTextFields', false);
        $enableTextFields = $enableTextFields === 'true' ? true : false;

        $entityId = intval($request->query->get('entityId', null));
        $entityId = !empty($entityId) && is_int($entityId) ? $entityId : null;

        $galleryRowIndex = intval($request->query->get('galleryRowIndex', null));
        $galleryRowIndex = !empty($galleryRowIndex) && is_int($galleryRowIndex) ? $galleryRowIndex : null;

        $galleryEntityProperty = $request->query->get('galleryEntityProperty', null);
        $galleryEntityProperty = !empty($galleryEntityProperty) ? $galleryEntityProperty : null;

        $galleryEntityName = $galleryEntityProperty ? explode('_', $galleryEntityProperty) : null;
        $galleryEntityName = $galleryEntityName && count($galleryEntityName) > 0 ? $galleryEntityName[0] : null;

        $galleryData = null;
        $galleryType = null;

        if ($entityId) {
            $entity = null;

            if ($galleryEntityName === 'Page') {
                $entity = $this->pageRepository->find($entityId);
            } elseif ($galleryEntityName === 'Post') {
                $entity = $this->postRepository->find($entityId);
            }

            if ($entity) {
                if ($galleryEntityProperty === 'Page_desktopSliderGalleryData') {
                    $galleryData = $entity->getDesktopSliderGalleryData();
                } elseif ($galleryEntityProperty === 'Page_mobileSliderGalleryData') {
                    $galleryData = $entity->getMobileSliderGalleryData();
                } elseif ($galleryEntityProperty === 'Post_desktopSliderGalleryData') {
                    $galleryData = $entity->getDesktopSliderGalleryData();
                } elseif ($galleryEntityProperty === 'Post_mobileSliderGalleryData') {
                    $galleryData = $entity->getMobileSliderGalleryData();
                }

                if ($galleryData && !empty($galleryData['data'])) {
                    $galleryData['data'] = json_decode($galleryData['data'], true);

                    foreach ($galleryData['data'] as $galleryColumnId => $galleryColumn) {
                        uasort($galleryColumn, fn ($a, $b) => $a['jsonData']['order'] - $b['jsonData']['order']);

                        $galleryData['data'][$galleryColumnId] = $galleryColumn;
                    }

                    $galleryData['order'] = intval($galleryData['order']);

                    $galleryType = 'gallery';
                }
            }
        }

        $mediaTemplateContent = $this->renderView('admin/mediaLibrary/gallery_media.html.twig', [
            'title' => 'Media Library',
            'enableTextFields' => $enableTextFields,
            'galleryType' => $galleryType,
            'galleryData' => $galleryData,
            'galleryRowIndex' => $galleryRowIndex,
            'mediaCategories' => $mediaCategories,
            'categoryId' => null,
            'pagesCount' => $mediaListPerPages['pagesCount'],
            'totalItems' => $mediaListPerPages['totalItems'],
            'currentPage' => $mediaListPerPages['currentPage'],
            'previousPage' => $mediaListPerPages['previousPage'],
            'nextPage' => $mediaListPerPages['nextPage'],
            'mediaItems' => $mediaListPerPages['items'],
        ]);

        return new JsonResponse([
            'forms' => [
                'pagesCount' => $mediaListPerPages['pagesCount'],
                'totalItems' => $mediaListPerPages['totalItems'],
                'currentPage' => $mediaListPerPages['currentPage'],
                'previousPage' => $mediaListPerPages['previousPage'],
                'nextPage' => $mediaListPerPages['nextPage'],
                'libraryHtmlTab' => $mediaTemplateContent,
            ],
        ]);
    }

    #[Route('/gallery-image-picker', name: 'gallery_image_picker')]
    public function galleryImagePicker(Request $request): JsonResponse
    {
        $page = intval($request->query->get('page', 1));
        $page = $page > 0 ? $page : 1;

        $categoryId = intval($request->query->get('categoryId', 0));
        $categoryId = $categoryId > 0 ? $categoryId : null;

        $media = new Media();

        $mediaListPerPages = $this->mediaRepository->findPerPage($page, $categoryId);
        $mediaCategories = $this->mediaCategoryRepository->findAll();

        $uploadMediaUploadForm = $this->createForm(UploadMediaLibraryType::class, $media);

        $mediaTemplateContent = $this->renderView('admin/mediaLibrary/gallery_image_picker.html.twig', [
            'title' => 'Media Library',
            'uploadMediaUploadForm' => $uploadMediaUploadForm,
            'mediaCategories' => $mediaCategories,
            'categoryId' => $categoryId,
            'pagesCount' => $mediaListPerPages['pagesCount'],
            'totalItems' => $mediaListPerPages['totalItems'],
            'currentPage' => $mediaListPerPages['currentPage'],
            'previousPage' => $mediaListPerPages['previousPage'],
            'nextPage' => $mediaListPerPages['nextPage'],
            'mediaItems' => $mediaListPerPages['items'],
        ]);

        return new JsonResponse([
            'forms' => [
                'pagesCount' => $mediaListPerPages['pagesCount'],
                'totalItems' => $mediaListPerPages['totalItems'],
                'currentPage' => $mediaListPerPages['currentPage'],
                'previousPage' => $mediaListPerPages['previousPage'],
                'nextPage' => $mediaListPerPages['nextPage'],
                'libraryHtmlTab' => $mediaTemplateContent,
            ],
        ]);
    }

    #[Route('/save-gallery-data', name: 'save_gallery_data', methods: ['POST'])]
    public function saveGalleryData(Request $request): JsonResponse
    {
        $entityId = intval($request->request->get('entityId', 0));

        $galleryEntityProperty = $request->request->get('galleryEntityProperty', null);
        $galleryEntityProperty = !empty($galleryEntityProperty) ? $galleryEntityProperty : null;

        $galleryEntityName = $galleryEntityProperty ? explode('_', $galleryEntityProperty) : null;
        $galleryEntityName = $galleryEntityName && count($galleryEntityName) > 0 ? $galleryEntityName[0] : null;

        $galleryData = $request->request->all($galleryEntityName, null);

        $entity = null;
        if ($galleryEntityName === 'Page') {
            $entity = $this->pageRepository->find($entityId);
        } elseif ($galleryEntityName === 'Post') {
            $entity = $this->postRepository->find($entityId);
        }

        /**
         * @var Post|Page
         */
        if (!$entity) {
            throw $this->createNotFoundException('El recursos solicitado no existe.');
        }

        if ($galleryEntityProperty === 'Page_desktopSliderGalleryData') {
            $entity->setDesktopSliderGalleryData($galleryData['desktopSliderGalleryData']);
        } elseif ($galleryEntityProperty === 'Page_mobileSliderGalleryData') {
            $entity->setMobileSliderGalleryData($galleryData['mobileSliderGalleryData']);
        } elseif ($galleryEntityProperty === 'Post_desktopSliderGalleryData') {
            $entity->setDesktopSliderGalleryData($galleryData['desktopSliderGalleryData']);
        } elseif ($galleryEntityProperty === 'Post_mobileSliderGalleryData') {
            $entity->setMobileSliderGalleryData($galleryData['mobileSliderGalleryData']);
        }

        $this->em->persist($entity);
        $this->em->flush();

        return new JsonResponse([
            'galleryData' => $galleryData,
        ]);
    }
}
