<?php

namespace App\Controller;


use App\Entity\Carousel;
use App\Entity\Date;
use App\Entity\Food;
use App\Entity\Menu;
use App\Entity\Offer;
use App\Entity\Template;
use App\Form\CardType;
use App\Form\DatesType;
use App\Form\ImageType;
use App\Form\MenusType;
use App\Form\TemplateType;
use App\ImageOptimizer;
use App\Repository\CarouselRepository;
use App\Repository\DateRepository;
use App\Repository\FoodRepository;
use App\Repository\MenuRepository;
use App\Repository\OfferRepository;
use App\Repository\ScheduleRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Yaml\Yaml;


class ClientController extends AbstractController 
{
    private function checkPattern($expression, $pattern) 
    {
        return preg_match($pattern, $expression);
    }
    private function handlePassword($request, $password, $userPasswordHasher, $user, $em) {
        $oldPassword = $request->request->get('oldPassword');
        if (!$this->checkPattern($password, "/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,80}$/")) {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        }

        // Check old password && user.password are the same
        if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)){
            return new JsonResponse([
                'result' => 'error_invalid'
            ]);
        }
        $encodePassword = $userPasswordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($encodePassword);

        try {
            $em->persist($user);
            $em->flush();
        } catch (Exception $e) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }
        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    private function handleEmail($email, $password, $userPasswordHasher, $user, $request, $repository) {
        // Check email pattern
        //  https://www.php.net/manual/en/filter.filters.validate.php
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        }

        //  Check same password
        if (!$userPasswordHasher->isPasswordValid($user, $password)) {
            $request->getSession()->set('last_email', $email);

            return new JsonResponse([
                'result' => 'error_invalid'
            ]);
        }
        // Check if another user exist with the same emaila address
        $other_user = $repository->findBy([
            'email' => $email
        ]);
        if ($other_user) {
            return 'error_email';
        }
        $user->setEmail($email);

        try {
            $repository->save($user, true);
        } catch (Exception $e) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }

        $request->getSession()->remove('last_email');
        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    #[Route('client/profil/{page_down}', name: 'client_profil', defaults: ['page_down' => 'fiches'])]
    public function profil(string $page_down, Request $request, ManagerRegistry $doctrine, 
                           UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository,
                           TemplateRepository $templateRepository) {
        
        $user = $this->getUser();

        // Last email use when try to to modify informations
        $last_email = $request->getSession()->get('last_email');

        // Sheets
        $template = new Template;
        $form_sheet = $this->createForm(TemplateType::class, $template);
        $form_sheet->handleRequest($request);
        if ($form_sheet->isSubmitted() && $form_sheet->isValid()) {
            try {
                $template = $form_sheet->getData();
                $template->setClient($user);
                $templateRepository->save($template, true);
            } catch (Exception $e) {
                return $this->redirectToRoute('client_profil', [
                    'page_down' => $page_down, 
                    'result' => 'error'
                ]);
            }
            return $this->redirectToRoute('client_profil', [
                'page_down' => $page_down,
                'result' => 'success'
            ]);
        } else if ($form_sheet->isSubmitted() && !$form_sheet->isValid()) {
            $request->query->remove('result');
        }

        if ($request->isMethod('POST')) {
            $em = $doctrine->getManager();

            $user = $this->getUser();

            // Informations
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            // Change password
            if ($password && !$email) {
                return $this->handlePassword($request, $password, $userPasswordHasher, $user, $em);
            }
            // Change email
            if ($email && $password) {
                return $this->handleEmail($email, $password, $userPasswordHasher, $user, $request, $userRepository);
            } 
        }

        $result = $request->query->get('result');
        dump($request);
        dump($result);
        switch($result) {
            case 'success' : 
                $success = "Modification effectuée avec succès !";
                break;
            case 'success_delete_past_dates':
                $success = "Les dates passées ont bien été supprimées.";
                break;
            case 'error_no_date':
                $error = "Aucune date n'a été trouvé.";
                break;
            case 'error_date_already_exist':
                $error = 'La date donnée existe déjà.';
                break;
            case 'error_email_email' : 
                $error = "Email invalide.";
                break;
            case 'error_invalid':
                $error = "Mot de passe incorrect.";
                break;
            case 'error_pattern':
                $error = "L'entrée fournie ne correspond pas au format requis.";
                break;
            case 'error' : 
                $error = "Un problème est survenu. Veuillez nous excuser pour la gêne occasionnée. Si le problème persiste, n'hésitez pas à nous contacter directement.";
                break;
            default : 
                break;
        }

        return $this->render('Client/profil.html.twig', [
            'page_down' => $page_down ,
            'error' => $error ?? null,
            'success' => $success ?? null,
            'last_email' => $last_email ?? '',
            'form_sheet' => $form_sheet->createView()
        ]);
    }
}