<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login/{reset}', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils, Request $request, $reset = 0): Response
    {
        // get the login error if there's one 
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastEmail = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);
        if ($form ->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('Login/login.form.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
            "reset" => $reset
            // 'form' => $form->createView()
        ]);
        
    }
}
