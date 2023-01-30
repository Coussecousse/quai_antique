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
    #[Route('/sign-up', name:'sign_up')]
    public function signIn(UserPasswordHasherInterface $userPasswordHasher, Request $request, ManagerRegistry $doctrine ) : Response 
    {
        $user = new User($userPasswordHasher);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $randomCode = rand(10, 20);
            $user->setCode($randomCode);
            $em->persist($user);
            $em->flush();

            

            return $this->redirectToRoute("validate_signUp");
        }

        return $this->render('SignUp/sign.html.twig', [
            'form' => $form->createView()
        ]);
    }
}  