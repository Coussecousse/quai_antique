<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PostController extends AbstractController
{
    #[Route('/', name:'home')]
    public function home()
    {
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
                ->from('from@example.com')
                ->to('to@example.com')
                ->subject($subject.' cherche à vous contacter !')
                ->text($form->get('message')->getData());
                // ->html('');

            dump($email);
            $mailer->send($email);
            return $this->render('Contact/contact.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        return $this->render('Contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}