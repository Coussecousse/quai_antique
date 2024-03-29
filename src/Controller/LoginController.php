<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/connexion/{login}', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request, $login = 0): Response
    {
        // get the login error if there's one 
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastEmail = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        return $this->render('Login/login.form.html.twig', [
            'last_email' => $lastEmail ?? null,
            'error' => $error ?? null,
            "login" => $login,
        ]);
        
    }
    #[Route('/déconnexion', name:'logout', methods : ['GET'])]
    public function logout()
    {
    }
}
