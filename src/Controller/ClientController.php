<?php

namespace App\Controller;

use App\Entity\Template;
use App\Form\TemplateType;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
    private function handleTemplate($request, $id, $repository) {
        $title = json_decode($request->getContent(), true)['template_title'];
        $name = json_decode($request->getContent(), true)['template_name'];
        $places = json_decode($request->getContent(), true)['template_places'];
        $allergies = json_decode($request->getContent(), true)['template_allergies'];

        $authorized_allergies = ['gluten', 'fish', 'shellfish', 'eggs', 'peanuts', 'mustard',
                                 'molluscs', 'soy', 'sulphites', 'sesame', 'celery', 'lupines', 
                                 'milk', 'nuts'];

        if (!$this->checkPattern($title, '/^[\p{L}\d\s.\'’()-]+$/u') ||
            !$this->checkPattern($name, '/^[\p{L}\d\s.\'’()-]+$/u') || 
            !$this->checkPattern($places, ' /^\d+$/')) 
        {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        }
        
        if (count($allergies) > 0) {
            foreach($allergies as $allergie){
                if (!in_array($allergie, $authorized_allergies)){
                    return new JsonResponse([
                        'result' => 'error'
                    ]);
                }
            }
        }
        try {
            $template = $repository->find($id);
            $template->setTitle($title)->setName($name)->setPlace($places)->setAllergies($allergies);
            $repository->save($template, true);
        } catch (Exception $e) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }
        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    private function deleteTemplate($request, $repository) {
        try {
            $id = $request->request->get('template_id');
            $template = $repository->find($id);
            $repository->remove($template, true);
        } catch (Exception $e) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }
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

        // Templates
        $template = new Template;
        $form_template = $this->createForm(TemplateType::class, $template);
        $form_template->handleRequest($request);
        $templates = $templateRepository->findBy(['client' => $user]);

        if ($form_template->isSubmitted() && $form_template->isValid()) {
            try {
                $template = $form_template->getData();
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
        } else if ($form_template->isSubmitted() && !$form_template->isValid()) {
            $request->query->remove('result');
        }

        if ($request->isMethod('POST')) {
            $em = $doctrine->getManager();

            $user = $this->getUser();

            // Informations
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            
            // Templates
            $id_template = json_decode($request->getContent(), true)['template_id'] ?? null;; 
            $delete_template = $request->request->get('template_delete');            

            // Change password
            if ($password && !$email) {
                return $this->handlePassword($request, $password, $userPasswordHasher, $user, $em);
            }
            // Change email
            if ($email && $password) {
                return $this->handleEmail($email, $password, $userPasswordHasher, $user, $request, $userRepository);
            } 

            // Change Template
            if ($id_template) {
                return $this->handleTemplate($request, $id_template, $templateRepository);
            }
            if ($delete_template) {
                return $this->deleteTemplate($request, $templateRepository);
            }

        }

        $result = $request->query->get('result');

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
            'form_sheet' => $form_template->createView(),
            'templates' => $templates,
        ]);
    }
}