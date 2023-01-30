<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController 
{
    #[Route('/sign-in', name:'sign_in')]
    public function signIn(UserPasswordHasherInterface $userPasswordHasher, Request $request, ManagerRegistry $doctrine ) : Response 
    {
        $user = new User($userPasswordHasher);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        return $this->render('SignIn/sign.html.twig', [
            'form' => $form->createView()
        ]);
    }
}  