<?php

namespace App\DataFixtures;

use App\Entity\Food;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'starter' => [
                [
                    'title' => 'Assiette de cochonnailles',
                    'description' => 'Jambon sec de Savoie, saucisson de Savoie, coppa (IGP)',
                    'price' => 13
                ],
                [
                    'title' => 'Demi-camembert roti sur lit de salade melée et son lard grillé',
                    'description' => 'Camembert au lait cru',
                    'price' => 15.50
                ],
                [
                    'title' => 'Escalope de foie gras poêlée au miel et compotée d’oignons doux des Cévennes (AOP)',
                    'description' => 'Miel de Savoie',
                    'price' => 28.00
                ],

            ],
            'main' => [
                [
                    'title' => 'L’authentique farçon savoyard maison',
                    'description' => 'Râpé de patates servie avec charcuterie, salade mêlée et sa sauce fromage blanc',
                    'price' => 19.90
                ],
                [
                    'title' => 'La Croziflette royale',
                    'description' => 'Gratiné de Crozet au sarrasin de fabrication artisanal, lardons, oignons, Reblochon, servie avec Jambon sec de Savoie et salade mêlée',
                    'price' => 28.00
                ],
                [
                    'title' => 'Marmite de crozets aux noix de saint Jacques',
                    'description' => 'Sauce crustacées, gratinée au beaufort',
                    'price' => 29.40
                ],
                [
                    'title' => 'Fondue traditionnelle',
                    'description' => 'Servie avec sa salade mêlée et patates, mini 2pers, prix par pers',
                    'price' => 29.90
                ],
                [
                    'title' => 'Fondue Royale',
                    'description' => 'Fondue aux cèpes et Jambon sec de Savoie, mini 2pers, prix par pers',
                    'price' => 37.90
                ],
            ],
            'dessert' => [
                [
                    'title' => 'Nougat glace et son coulis de myrtilles',
                    'description' => '',
                    'price' => 8.90
                ],
                [
                    'title' => 'Coupe des montagnes',
                    'description' => 'Glace yaourt, coulis de myrtiles, myrtilles, chantilly',
                    'price' => 10.50
                ],
                [
                    'title' => 'Assortiment de fromages secs de Savoie',
                    'description' => 'Beaufort et Roblochon',
                    'price' => 10.80
                ],
            ]
        ];

        foreach($categories as $name => $categorie) {
            foreach($categorie as $food) {
                $newFood = new Food();
                $newFood->setCategory($name)
                        ->setTitle($food['title'])
                        ->setDescription($food['description'])
                        ->setPrice($food['price']);

                $manager->persist($newFood);
            }
        }

        $manager->flush();
    }
}
