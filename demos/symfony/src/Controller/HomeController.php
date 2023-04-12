<?php

namespace App\Controller;

use App\Entity\PageTranslations;
use App\Repository\PageRepository;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale}')]
class HomeController extends AbstractController
{
    public function __construct(
        private PostRepository $postRepository,
        private PageRepository $pageRepository,
    ) {
    }

    #[
        Route(
            [
                'es' => '/inicio',
                'en' => '/home',
            ],
            name: 'home-page',
        ),
    ]
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();

        $homePage = $this->pageRepository->findOneBySlugs(['home', 'inicio']);
        /**
         * @var Page
         */
        $homePageLocale = $homePage
            ->getTranslations()
            ->filter(function (PageTranslations $pageTrans) use ($locale) {
                return $pageTrans->getLanguageCode() === $locale;
            })
            ->first();

        $desktopSlider = [];
        $desktopSliderData = $homePage->getDesktopSliderGalleryData();
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
        $mobileSliderData = $homePage->getMobileSliderGalleryData();
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

        $postsFeatured = $this->postRepository->findBy(['featured' => true]);

        $lastNews = $this->postRepository->findByDate(new DateTimeImmutable('now'), 5);

        return $this->render('home/index.html.twig', [
            'content' =>
            $homePageLocale && !empty($homePageLocale->getContent())
                ? $homePageLocale->getContent()
                : $homePage->getContent(),
            'desktopSlider' => $desktopSlider,
            'mobileSlider' => $mobileSlider,
            'postsFeatured' => $postsFeatured,
            'lastNews' => $lastNews,
        ]);
    }
}
