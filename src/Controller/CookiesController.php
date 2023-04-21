<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CookiesController extends AbstractController
{
    #[Route('/cookies', name:'cookies')]
    public function cookie(Request $request) {

        return $this->render('Cookies/cookies.html.twig', [
        ]);
    }
}