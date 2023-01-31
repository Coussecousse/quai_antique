<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController 
{
    #[Route('/sign-up', name:'sign_up')]
    public function signIn(UserPasswordHasherInterface $userPasswordHasher, Request $request, ManagerRegistry $doctrine, MailerInterface $mailer ) : Response 
    {
        $user = new User($userPasswordHasher);
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();
            $randomCode = rand(1, 1000000000);
            $user->setCode($randomCode);
            $em->persist($user);
            $em->flush();

            $email = (new Email())
                ->from('from@example.com')
                ->to($form->get('email')->getData())
                ->subject("Validez votre compte sur le site du Quai Antique !")
                ->text("Cher utilisateur, \n
                Merci de vous être enregistré sur notre site. Pour valider votre compte, veuillez utiliser le code suivant :\n
                Code de validation : ".$randomCode.
                "\nCliquez sur ce lien pour activer votre compte : https://127.0.0.1:8000/sign-up/validate \n
                Si vous n'êtes pas à l'origine de cette demande d'inscription, veuillez ignorer ce message.\n
                Cordialement,\n
                Le Quai Antique."
            );

            try {
                $mailer->send($email);
                return $this->redirectToRoute('signUp-mail', [
                    "result" => "success"
                ]);
            } catch (TransportExceptionInterface $e) {
                return $this->redirectToRoute('signUp-mail', [
                    "result" => "error"
                ]);
            }
        }

        return $this->render('SignUp/signUp.form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/sign-up/{result}', name : "signUp-mail")]
    public function contactSuccess($result) {

        dump($result);

        return $this->render('SignUp/signUp.mail.html.twig', [
            "result" => $result
        ]);
    }
}  

