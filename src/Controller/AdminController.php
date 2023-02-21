<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('admin/profil/{page}', name: "admin_profil")]
    public function profil($page, Request $request)
    {
        if ($request) {
            dump($request);
        }
        return $this->render('Admin/profil.html.twig', [
            'page' => $page
        ]);
    }
}