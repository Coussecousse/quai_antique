<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use App\Entity\Offer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MenuFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $menus = [
                'Menu du Jour' => [
                    [
                        'title' => 'Formule du travailleur',
                        'conditions' => '-Du lundi au samedi midi-',
                        'description' => ['Entrée du jour', 'Plat du jour', 'Dessert du jour'],
                        'price' => 30
                    ],
                    [
                        'title' => 'Formule complète du travailleur',
                        'conditions' => '-Du lundi au samedi midi-',
                        'description' => ['Apéritif', 'Vin 38cl', 'Entrée du jour', 'Plat du jour', 'Dessert du jour', 'Café'],
                        'price' => 42
                    ],
                ],
                'Menu du touriste' => [
                    [
                        'title' => 'Formule découverte',
                        'conditions' => null,
                        'description' => ['Assiette de cochonailles', 'L’authentique farçon savoyard maison', 'Assortiment de fromages secs de Savoie'],
                        'price' => 48
                    ],
                    [
                        'title' => 'Formule Royale',
                        'conditions' => null,
                        'description' => ['Escalope de foie gras poêlée au miel ', 'Fondue Royale', 'Coupe des montagnes'],
                        'price' => 67
                    ]
                ]
        ];

        foreach($menus as $name => $menu){
            $newMenu = new Menu();
            $newMenu->setTitle($name);
            foreach($menu as $offer) {
                $newOffer = new Offer();
                $newOffer->setTitle($offer['title'])->setDescription($offer['description'])->setConditions($offer['conditions'])->setPrice($offer['price']);
                $newMenu->addOffer($newOffer);
            }
            $manager->persist($newMenu);
        }
        
        $manager->flush();
    }
}
