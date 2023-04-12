<?php

namespace App\Controller;

use App\Entity\PostTranslations;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}')]
class PostController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    #[
        Route(
            [
                'es' => '/post/{slug}/{publishedAt}',
                'en' => '/post/{slug}/{publishedAt}',
            ],
            name: 'post-index-page',
            requirements: [
                'slug' => '[a-z-A-Z0-9\-]+',
                'publishedAt' => '(\d{4})-(\d{2})-(\d{2})',
            ],
            defaults: ['publishedAt' => null],
        ),
    ]
    public function index(Request $request, string $slug): Response
    {
        $post = $this->postRepository->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('El recursos solicitado no existe.');
        }

        $locale = $request->getLocale();

        $postLocale = $post
            ->getTranslations()
            ->filter(function (PostTranslations $postTrans) use ($locale) {
                return $postTrans->getLanguageCode() === $locale;
            })
            ->first();

        $desktopSlider = [];
        $desktopSliderData = $post->getDesktopSliderGalleryData();
        if (!empty($desktopSliderData) && !empty($desktopSliderData['data'])) {
            $desktopSliderData['data'] = !is_array($desktopSliderData['data'])
                ? json_decode($desktopSliderData['data'], true)
                : $desktopSliderData['data'];

            foreach ($desktopSliderData['data'] as $galleryColumnId => $galleryColumn) {
                uasort($galleryColumn, fn ($a, $b) => $a['jsonData']['order'] - $b['jsonData']['order']);

                $desktopSliderData['data'][$galleryColumnId] = $galleryColumn;
            }

            $desktopSliderData['order'] = intval($desktopSliderData['order']);

            $desktopSlider = $desktopSliderData;
        }

        $mobileSlider = [];
        $mobileSliderData = $post->getMobileSliderGalleryData();
        if (!empty($mobileSliderData) && !empty($mobileSliderData['data'])) {
            $mobileSliderData['data'] = !is_array($mobileSliderData['data'])
                ? json_decode($mobileSliderData['data'], true)
                : $mobileSliderData['data'];

            foreach ($mobileSliderData['data'] as $galleryColumnId => $galleryColumn) {
                uasort($galleryColumn, fn ($a, $b) => $a['jsonData']['order'] - $b['jsonData']['order']);

                $mobileSliderData['data'][$galleryColumnId] = $galleryColumn;
            }

            $mobileSliderData['order'] = intval($mobileSliderData['order']);

            $mobileSlider = $mobileSliderData;
        }

        $lastNews = $this->postRepository->findByDate(new DateTimeImmutable('now'), 5, $post);

        return $this->render('post/index.html.twig', [
            'slug' => $slug,
            'post' => $post,
            'postLocale' => $postLocale ?? $post,
            'lastNews' => $lastNews,
            'desktopSlider' => $desktopSlider,
            'mobileSlider' => $mobileSlider,
        ]);
    }
}
