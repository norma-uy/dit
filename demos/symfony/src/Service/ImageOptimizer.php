<?php
namespace App\Service;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer
{
    private $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function widthResize(string $filename, int $width, string $newFilName = null): void
    {
        [$iwidth, $iheight] = getimagesize($filename);
        $ratio = $iwidth / $iheight;

        $height = $width / $ratio;

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($newFilName ? $newFilName : $filename);
    }

    public function resize(string $filename, int $width, int $height, string $newFilName = null): void
    {
        [$iwidth, $iheight] = getimagesize($filename);
        $ratio = $iwidth / $iheight;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($newFilName ? $newFilName : $filename);
    }
}
