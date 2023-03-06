<?php

namespace App;

use App\Entity\Carousel;
use Doctrine\Persistence\ManagerRegistry;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;

class ImageOptimizer 
{
    private const MAX_HEIGHT = 2200 ;
    private $imagine;
    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    public function resize(string $filename, int $width)
    {
        list($iwidth, $iheight) = getimagesize($filename);
        
        $ratio = $iwidth / $iheight;
        $height = self::MAX_HEIGHT;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $image = $this->imagine->open($filename);
        $image->resize(new Box($width, $height))->save($filename);
    }
}