<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController 
{
    #[Route('/error/{id}', name: 'error')]
    public function error($id)
    {
        return $this->render('Error/error.html.twig', [
            
        ]);
    }
}