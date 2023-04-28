<?php

namespace App\DataFixtures;

use App\Entity\Carousel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarouselFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $images = [
            [
                'path' => '/images/resize/barista-5055060-960-720-644b8b6f0060f.jpg',
                'title' => 'Irish Coffee',
                'description' => 'Homme versant du café dans des verres transparents.',
            ],
            [
                'path' => '/images/resize/nadine-primeau-xLskrI8Dw68-unsplash-644b8b7c171ff.jpg',
                'title' => 'Coupe des montagnes',
                'description' => 'Coupe de glace à la myrtille.',
            ],
            [
                'path' => '/images/resize/pexels-matheus-bertelli-7510801-1-644b8bda718ae.jpg',
                'title'=> 'Fondue Royale',
                'description' => 'Casserole rouge contenant du fromage avec une personne tenant du pain au dessus.',
            ]
        ];

        foreach($images as $image) {
            $newImage = new Carousel();
            $newImage->setPath($image['path'])->setTitle($image['title'])->setDescription($image['description']);
            
            $manager->persist($newImage);
        }
        

        $manager->flush();
    }
}

