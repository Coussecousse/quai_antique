<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
    #[Route('/', name:'home')]
    public function home(UserInterface $user, UserRepository $repository)
    {
        if ($user) {
            $email = $user->getUserIdentifier();
            
            $result = $repository->getUser($email);

            if(!in_array('ROLE_VERIFIED', $result->getRoles())) {{
                return $this->redirectToRoute('signUp-validate');
            }}
        }
        return $this->render('Home/home.html.twig');
    }

    #[Route('/carte', name:'card')]
    public function card() {
        return $this->render('Card/card.html.twig');
    }
    #[Route('/carte/entrées', name:'card-starter')]
    public function cardStarter() {
        return $this->render('Card/Starter/starter.card.html.twig');
    }
    #[Route('/carte/plats', name:'card-main')]
    public function cardMain() {
        return $this->render('Card/Main/main.card.html.twig');
    }
    #[Route('/carte/desserts', name:'card-dessert')]
    public function cardDessert() {
        return $this->render('Card/Dessert/dessert.card.html.twig');
    }
    #[Route('/carte/menus', name:'card-menus')]
    public function cardMenus() {
        return $this->render('Card/Menus/menus.card.html.twig');
    }
    #[Route('/contact', name:'contact')]
    public function contact(Request $request, MailerInterface $mailer) {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $subject = $form->get('name')->getData();
        

        if ($form->isSubmitted() && $form->isValid() ) {
            dump($form);
            $email = (new Email())
                ->from('restaurant@quai-antique.fr')
                ->to('restaurant@quai-antique.fr')
                ->subject($subject.' cherche à vous contacter !')
                ->text($form->get('message')->getData());
                // ->html('');

            try {
                $mailer->send($email);
                return $this->redirectToRoute('contact-result', [
                    "result" => "success",
                ]);
            } catch (TransportExceptionInterface $e) {
                return $this->redirectToRoute('contact-result', [
                    "result" => "error",
                ]);
            }
        }

        return $this->render('Contact/contact.form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/contact/{result}', name:'contact-result')]
    public function contactResult($result) {

        dump($result);

        return $this->render('Contact/contact.result.html.twig', [
            "result" => $result
        ]);
    }
}