<?php

namespace App\EventListener;

use App\Entity\Media;
use App\Service\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class MediaListener
{
    private AsciiSlugger $slugger;

    public function __construct(
        private ParameterBagInterface $params,
        private EntityManagerInterface $entityManager,
        private UploaderHelper $helper,
        private ImageOptimizer $imageOptimizer,
    ) {
        $this->slugger = new AsciiSlugger();
    }

    public function onVichUploaderPostUpload(Event $event)
    {
        $this->uploadFile($event);
    }

    public function onVichUploaderPostInject(Event $event)
    {
        // $this->uploadFile($event);
    }

    private function uploadFile(Event $event)
    {
        $media = $event->getObject();

        if (
            $media instanceof Media &&
            (str_contains($media->getMimeType(), 'image/') || str_contains($media->getMimeType(), 'video/'))
        ) {
            if (empty($media->getTitle())) {
                $originaName = pathinfo($media->getOriginalName(), PATHINFO_FILENAME);

                if (!empty($originaName)) {
                    $media->setTitle($originaName);
                    $media->setSlug($this->slugger->slug($media->getTitle())->lower());
                }
            }

            $filesystem = new Filesystem();

            $widthList = [
                '150w' => ['width' => 150, 'imageFileAttr' => 'imageFile150w'],
                '450w' => ['width' => 450, 'imageFileAttr' => 'imageFile450w'],
                '800w' => ['width' => 800, 'imageFileAttr' => 'imageFile800w'],
                '1280w' => ['width' => 1280, 'imageFileAttr' => 'imageFile1280w'],
                '1600w' => ['width' => 1600, 'imageFileAttr' => 'imageFile1600w'],
                '1920w' => ['width' => 1920, 'imageFileAttr' => 'imageFile1920w'],
                '2400w' => ['width' => 2400, 'imageFileAttr' => 'imageFile2400w'],
            ];

            $tmpFilesToDelete = [];

            foreach ($widthList as $rKey => $rData) {
                $rootProjectPath = getcwd();
                $rootProjectPath =
                    substr($rootProjectPath, -6) === 'public' ? $rootProjectPath : $rootProjectPath . '/public';

                $originalFilePath = $this->helper->asset($media, 'originalFile');
                $filePathParts = pathinfo($rootProjectPath . $originalFilePath);

                $tmpStoragePath = $this->params->get('tmp_storage_path');
                $targetFileName = "{$filePathParts['filename']}_{$rKey}.{$filePathParts['extension']}";
                $tmpTargetFilePath = "{$rootProjectPath}{$tmpStoragePath}/{$targetFileName}";

                $resizeEnabled = false;

                if (str_contains($media->getMimeType(), 'image/')) {
                    try {
                        $filesystem->copy($rootProjectPath . $originalFilePath, $tmpTargetFilePath, true);

                        $resizeEnabled = true;
                    } catch (Exception $e) {
                        $resizeEnabled = false;
                    }
                } elseif (str_contains($media->getMimeType(), 'video/')) {
                    try {
                        $ffmpeg = FFMpeg::create();
                        $video = $ffmpeg->open($rootProjectPath . $originalFilePath);
                        $frame = $video->frame(TimeCode::fromSeconds(1));
                        $frame->save($tmpTargetFilePath);

                        $resizeEnabled = true;
                    } catch (Exception $e) {
                        $resizeEnabled = false;
                    }
                }

                if ($resizeEnabled) {
                    $this->imageOptimizer->widthResize($tmpTargetFilePath, $rData['width']);

                    if ($rKey === '150w') {
                        $media->imageFile150w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    } elseif ($rKey === '450w') {
                        $media->imageFile450w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    } elseif ($rKey === '800w') {
                        $media->imageFile800w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    } elseif ($rKey === '1280w') {
                        $media->imageFile1280w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    } elseif ($rKey === '1600w') {
                        $media->imageFile1600w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    } elseif ($rKey === '1920w') {
                        $media->imageFile1920w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    } elseif ($rKey === '2400w') {
                        $media->imageFile2400w = new UploadedFile($tmpTargetFilePath, $targetFileName);
                    }

                    $tmpFilesToDelete[] = $targetFileName;
                }
            }

            /**
             * Borrar los temporales sin uso
             */
            $tmpFinder = new Finder();

            $tmpFinder
                ->files()
                ->in($rootProjectPath . '/storage/tmp')
                ->notName(['.gitignore', ...$tmpFilesToDelete]);

            foreach ($tmpFinder as $tmpFile) {
                $absoluteFilePath = $tmpFile->getRealPath();

                @unlink($absoluteFilePath);
            }
        }
    }
}
