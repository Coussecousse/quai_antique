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
    private $doctrine;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->imagine = new Imagine();
        $this->doctrine = $doctrine;
    }

    public function resize(string $filename,string $path, int $width,string $sizeName, string $title)
    {
        $path = $path.$filename;

        $em = $this->doctrine->getManager();

        list($iwidth, $iheight) = getimagesize($path);
        
        $ratio = $iwidth / $iheight;
        $height = self::MAX_HEIGHT;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $image = $this->imagine->open($path);
        $image->resize(new Box($width, $height))->save($path);

        $carousel = new Carousel();
        $carousel->setPath('/build/images/resize/'.$filename)->setSize($sizeName)->setTitle($title);
        $em->persist($carousel);
        $em->flush();
    }
}