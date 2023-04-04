<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name:'contact')]
    public function contact(Request $request, MailerInterface $mailer) {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $subject = $form->get('name')->getData();
        

        if ($form->isSubmitted() && $form->isValid() ) {
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

        return $this->render('Contact/contact.result.html.twig', [
            "result" => $result
        ]);
    }
}