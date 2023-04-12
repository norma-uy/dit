<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\MediaCategory;
use App\Entity\Page;
use App\Entity\PageTranslations;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AppFixtures extends Fixture
{
    private array $pages;

    public function __construct(private UserPasswordHasherInterface $passwordHasher, private UploaderHelper $helper)
    {
        $this->pages = [
            'Home' => [
                'title' => [
                    'es' => 'Inicio',
                    'en' => 'Home',
                ],
                'desktopSlider' => [
                    getcwd() . '/assets/demo/images/slider/banner-1-desktop.webp',
                    getcwd() . '/assets/demo/images/slider/banner-2-desktop.webp',
                    getcwd() . '/assets/demo/images/slider/banner-3-desktop.webp',
                    getcwd() . '/assets/demo/images/slider/banner-4-desktop.webp',
                ],
                'mobileSlider' => [
                    getcwd() . '/assets/demo/images/slider/banner-1-mobile.jpg',
                    getcwd() . '/assets/demo/images/slider/banner-2-mobile.jpg',
                    getcwd() . '/assets/demo/images/slider/banner-3-mobile.jpg',
                    getcwd() . '/assets/demo/images/slider/banner-4-mobile.jpg',
                ],
            ],
            'Contact' => [
                'title' => [
                    'es' => 'Contacto',
                    'en' => 'Contact',
                ],
                'desktopSlider' => [],
                'mobileSlider' => [],
            ],
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        //Master user
        $masterUser = new User();
        $masterUser
            ->setEmail('master@admincms.com.uy')
            ->setRoles(['ROLE_SUPER_ADMIN'])
            ->setIsVerified(true)
            ->setName('Master')
            ->setUsername('master@admincms.com.uy')
            ->setPassword($this->passwordHasher->hashPassword($masterUser, 'master@admincms.com.uy'));
        $manager->persist($masterUser);

        //Pages
        foreach ($this->pages as $title => $data) {
            $page = new Page();

            $desktopSlider = [
                'order' => '0',
                'templateType' => 'full-width',
                'data' => [
                    '1' => [],
                ],
            ];
            foreach ($data['desktopSlider'] as $mediaIndex => $filePath) {
                $fileInfo = pathinfo($filePath);
                $sliderPhoto = new Media();
                $sliderPhoto
                    ->setOriginalFile(new UploadedFile($filePath, $fileInfo['basename']))
                    ->setOriginalFileName($fileInfo['basename'])
                    ->setTitle("$title-$mediaIndex")
                    ->setAltText("$title-$mediaIndex")
                    ->setSlug($slugger->slug($sliderPhoto->getTitle())->lower())
                    ->setCreatedAt(new \DateTimeImmutable('now'))
                    ->setAuthor($masterUser);
                $manager->persist($sliderPhoto);
                $manager->flush();

                $desktopSlider['order'] = '0';
                $desktopSlider['templateType'] = 'full-width';
                $desktopSlider['data']['1'][$sliderPhoto->getId()] = [
                    'jsonData' => [
                        'order' => '0',
                        'mediaTitle' => $sliderPhoto->getTitle(),
                        'mimeType' => $sliderPhoto->getMimeType(),
                        'originalFile' => $this->helper->asset($sliderPhoto, 'originalFile'),
                        'imageFile-2400w' => $this->helper->asset($sliderPhoto, 'imageFile2400w'),
                        'imageFile-1920w' => $this->helper->asset($sliderPhoto, 'imageFile1920w'),
                        'imageFile-1600w' => $this->helper->asset($sliderPhoto, 'imageFile1600w'),
                        'imageFile-1280w' => $this->helper->asset($sliderPhoto, 'imageFile1280w'),
                        'imageFile-800w' => $this->helper->asset($sliderPhoto, 'imageFile800w'),
                        'imageFile-450w' => $this->helper->asset($sliderPhoto, 'imageFile450w'),
                        'imageFile-150w' => $this->helper->asset($sliderPhoto, 'imageFile150w'),
                    ],
                    'mediaItemHtml' => [],
                ];
            }
            $desktopSlider['data'] = json_encode($desktopSlider['data']);

            $mobileSlider = [
                'order' => '0',
                'templateType' => 'full-width',
                'data' => [
                    '1' => [],
                ],
            ];
            foreach ($data['mobileSlider'] as $mediaIndex => $filePath) {
                $fileInfo = pathinfo($filePath);
                $sliderPhoto = new Media();
                $sliderPhoto
                    ->setOriginalFile(new UploadedFile($filePath, $fileInfo['basename']))
                    ->setOriginalFileName($fileInfo['basename'])
                    ->setTitle("$title-$mediaIndex")
                    ->setAltText("$title-$mediaIndex")
                    ->setSlug($slugger->slug($sliderPhoto->getTitle())->lower())
                    ->setCreatedAt(new \DateTimeImmutable('now'))
                    ->setAuthor($masterUser);
                $manager->persist($sliderPhoto);
                $manager->flush();

                $mobileSlider['order'] = '0';
                $mobileSlider['templateType'] = 'full-width';
                $mobileSlider['data']['1'][$sliderPhoto->getId()] = [
                    'jsonData' => [
                        'order' => '0',
                        'mediaTitle' => $sliderPhoto->getTitle(),
                        'mimeType' => $sliderPhoto->getMimeType(),
                        'originalFile' => $this->helper->asset($sliderPhoto, 'originalFile'),
                        'imageFile-2400w' => $this->helper->asset($sliderPhoto, 'imageFile2400w'),
                        'imageFile-1920w' => $this->helper->asset($sliderPhoto, 'imageFile1920w'),
                        'imageFile-1600w' => $this->helper->asset($sliderPhoto, 'imageFile1600w'),
                        'imageFile-1280w' => $this->helper->asset($sliderPhoto, 'imageFile1280w'),
                        'imageFile-800w' => $this->helper->asset($sliderPhoto, 'imageFile800w'),
                        'imageFile-450w' => $this->helper->asset($sliderPhoto, 'imageFile450w'),
                        'imageFile-150w' => $this->helper->asset($sliderPhoto, 'imageFile150w'),
                    ],
                    'mediaItemHtml' => [],
                ];
            }
            $mobileSlider['data'] = json_encode($mobileSlider['data']);

            $pageEs = !empty($data['title']['es']) ? new PageTranslations() : null;
            if ($pageEs) {
                $pageEs
                    ->setTitle($data['title']['es'])
                    ->setSlug($slugger->slug($pageEs->getTitle())->lower())
                    ->setLanguageCode("es");

                $page->addTranslation($pageEs);
                $manager->persist($pageEs);
            }

            $pageEn = !empty($data['title']['en']) ? new PageTranslations() : null;
            if ($pageEn) {
                $pageEn
                    ->setTitle($data['title']['en'])
                    ->setSlug($slugger->slug($pageEn->getTitle())->lower())
                    ->setLanguageCode("en");

                $page->addTranslation($pageEn);
                $manager->persist($pageEn);
            }

            $page
                ->setTitle($title)
                ->setSlug($slugger->slug($page->getTitle())->lower())
                ->setDesktopSliderGalleryData($desktopSlider)
                ->setMobileSliderGalleryData($mobileSlider);
            $manager->persist($page);
        }

        //media category
        $thumbnailCategory = new MediaCategory();
        $thumbnailCategory->setTitle('miniatura')->setSlug($slugger->slug($thumbnailCategory->getTitle())->lower());
        $manager->persist($thumbnailCategory);

        $manager->flush();
    }
}
