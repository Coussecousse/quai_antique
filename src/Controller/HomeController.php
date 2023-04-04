<?php

namespace App\Controller;

use App\Repository\CarouselRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name:'home')]
    public function index(CarouselRepository $carouselRepository)
    {
        $images = $carouselRepository->findAll();
        
        if ($this->getUser()) {
            $roles =$this->getUser()->getRoles();

            if(!in_array('ROLE_VERIFIED', $roles)) {{
                return $this->redirectToRoute('signUp-validate');
            }}
        }

        return $this->render('Home/home.html.twig', [
            'images_carousel' => $images
        ]);
    }
}