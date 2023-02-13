<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController 
{
    #[Route('/sign-up', name:'sign_up')]
    public function signIn(UserPasswordHasherInterface $userPasswordHasher, Request $request, ManagerRegistry $doctrine, MailerInterface $mailer ) : Response 
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $doctrine->getManager();

            // Hash password 
            $encodePassword = $userPasswordHasher->hashPassword(
                $user, 
                $form->get('password')->getData()
            );
            $user->setPassword($encodePassword);

            // Random code for account validation
            $randomCode = rand(1, 1000000000);
            $user->setCode($randomCode);

            $em->persist($user);
            try {
                $em->flush();
                
            } catch (UniqueConstraintViolationException $e) {
                $error = "Un compte possédant cet email existe déjà.";
                return $this->render('SignUp/form/signUp.form.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            } catch (Exception $e) {
                $error = "Une erreur est survenue. Nous vous invitons à prendre contact directement avec nous.";
                
                return $this->render('SignUp/form/signUp.form.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }
            
            
            $email = (new TemplatedEmail())
                ->from('restaurant@quai-antique.fr')
                ->to($form->get('email')->getData())
                ->subject("Validez votre compte sur le site du Quai Antique !")
                ->htmlTemplate('SignUp/mail/email.html.twig')
                ->context([
                    'code' => $randomCode,
                ])
            ;
                
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

        return $this->render('SignUp/form/signUp.form.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);
    }

    #[Route('/sign-up/validate', name: 'signUp-validate')]
    public function signUpValidate(Request $request, UserRepository $repository) {
        
        $code = $request->query->get('code');
        if ($code) {
            try {
                $result =  $repository->findCode($code);
                $changeConfirm = $repository->setConfirm($code);

                return $this->redirectToRoute('login', [
                    "login" => 2
                ]);
            } catch ( NoResultException $e) {
                return $this->redirectToRoute('signUp-validate-result', [
                    "result" => "no-match"
                ]);
            }
        }
        
        return $this->render('SignUp/validate/signUp.validate.html.twig');
    }
    #[Route('/sign-up/validate/{result}', name : "signUp-validate-result")]
    public function signUpValidateResult($result, Request $request, UserRepository $repository) {

        $code = $request->query->get('code');
        if ($code) {
            try {
                $result =  $repository->findCode($code);
                $changeConfirm = $repository->setConfirm($code);

                return $this->redirectToRoute('signUp-validate-result', [
                    "result" => "match"
                ]);
            } catch ( NoResultException $e) {
                return $this->redirectToRoute('signUp-validate-result', [
                    "result" => "no-match"
                ]);
            }
        }
        return $this->render('SignUp/validate/signUp.validate.result.html.twig', [
            "result" => $result
        ]);
    }

    #[Route('/sign-up/{result}', name : "signUp-mail")]
    public function signUpResult($result) {
        return $this->render('SignUp/mail/signUp.mail.html.twig', [
            "result" => $result
        ]);
    }

}  

