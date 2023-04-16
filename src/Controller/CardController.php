<?php

namespace App\Controller;

use App\Repository\FoodRepository;
use App\Repository\MenuRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CardController extends AbstractController
{
    #[Route('/carte', name:'card')]
    public function card() {
        return $this->render('Card/card.html.twig');
    }
    #[Route('/carte/entrées', name:'card-starter')]
    public function cardStarter(FoodRepository $repository) {

        $foods = $repository->findBy(['category' => 'starter']);

        return $this->render('Card/Starter/starter.card.html.twig', [
            'foods' => $foods,
        ]);
    }
    #[Route('/carte/plats', name:'card-main')]
    public function cardMain(FoodRepository $repository) {

        $foods = $repository->findBy(['category' => 'main']);

        return $this->render('Card/Main/main.card.html.twig', [
            'foods'=> $foods
        ]);
    }
    #[Route('/carte/desserts', name:'card-dessert')]
    public function cardDessert(FoodRepository $repository) {

        $foods = $repository->findBy(['category' => 'dessert']);

        return $this->render('Card/Dessert/dessert.card.html.twig', [
            'foods'=> $foods
        ]);
    }
    #[Route('/carte/menus', name:'card-menus')]
    public function cardMenus(MenuRepository $repository) {

        $menus = $repository->findAll();;

        return $this->render('Card/Menus/menus.card.html.twig', [
            'menus' => $menus
        ]);
    }
}