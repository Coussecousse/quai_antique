<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Yaml\Yaml;

class ContactController extends AbstractController
{
    #[Route('/contact/{result}', name:'contact')]
    public function contact(Request $request, MailerInterface $mailer, $result = '') {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $name = $form->get('name')->getData();
        
        $datas = Yaml::parseFile($this->getParameter('data'));
        $email = $datas['email'];

        if ($form->isSubmitted() && $form->isValid() ) {
            $email = (new TemplatedEmail())
                ->from($email)
                ->to($email)
                ->subject($name.' cherche à vous contacter !')
                ->htmlTemplate('Contact/email.html.twig')
                ->context([
                    'name' => $name,
                    'tel' => $form->get('tel')->getData(),
                    'message' => $form->get('message')->getData()
                ]);

            try {
                $mailer->send($email);
                return $this->redirectToRoute('contact', [
                    "result" => "success",
                ]);
            } catch (TransportExceptionInterface $e) {
                return $this->redirectToRoute('contact', [
                    "result" => "error",
                ]);
            }
        }

        return $this->render('Contact/contact.html.twig', [
            'form' => $form->createView(),
            'result' => $result ?? ''
        ]);
    }
    #[Route('/contact/{result}', name:'contact-result')]
    public function contactResult($result) {

        return $this->render('Contact/contact.html.twig', [
            "result" => $result
        ]);
    }
}