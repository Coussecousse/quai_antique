<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/', name:'home')]
    public function home()
    {
        return $this->render('Home/home.html.twig');
    }

    #[Route('/carte', name:'card')]
    public function card() {
        return $this->render('Card/card.html.twig');
    }
    #[Route('/carte/entrées', name:'card-starter')]
    public function cardStarter() {
        return $this->render('Card/Starter/starter.card.html.twig');
    }
}