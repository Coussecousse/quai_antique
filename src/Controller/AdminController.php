<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('admin/profil/{page}', name: "admin_profil", methods: ['GET', 'POST'])]
    public function profil($page = 'carousel', UserRepository $repository, Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher)
    {
        $result = $request->query->get('result');

        switch($result) {
            case 'success_email' : 
                $success = "Email modifié avec succès !";
                break;
            case 'success_password' : 
                $success = "Mot de passe modifié avec succès !";
                break;
            case 'error_email_email' : 
                $error = "EMail invalide.";
                break;
            case 'error_invalid':
                $error = "Mot de passe incorrect.";
                break;
            case 'error' : 
                $error = "Un problème est survenu. Veuillez nous excuser pour la gêne occasionnée. Si le problème persiste, n'hésitez pas à nous contacter directement.";
                break;
            default : 
                break;
        }

        $last_email = $request->getSession()->get('last_email');
        
        if ($request->isMethod('POST')) {
            $em = $doctrine->getManager();

            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = $this->getUser();
            // Change password
            if ($password && !$email) {
                dump('hey');
                $oldPassword = $request->request->get('oldPassword');
                // Check password pattern

                // Check old password && user.password are the same
                if (!$userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                    $response = new JsonResponse([
                        'result' => 'error_invalid'
                    ]);
                    return $response;
                }
                $encodePassword = $userPasswordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($encodePassword);

                $em->persist($user);
                $em->flush();

                $response = new JsonResponse([
                    'result' => 'success',
                ]);
                return $response;
            }
            // Change email
            if ($email && $password) {
                //  Check  password
                if (!$userPasswordHasher->isPasswordValid($user, $password)){
                    $request->getSession()->set('last_email', $email);

                    $response  = new JsonResponse([
                        'result' => 'error_invalid'
                    ]);
                    return $response;
                }
                // Check if another user exist
                $other_user = $repository->findBy([
                    'email' => $email
                ]);
                if ($other_user != null) {
                    $response = new JsonResponse([
                        'result' => 'error_email',
                    ]);
                    return $response;
                }
                $user->setEmail($email);

                $em->persist($user);
                $em->flush();

                $request->getSession()->remove('last_email');

                $response = new JsonResponse([
                    'result' => 'success',
                ]);
                return $response;
            }    
        }

        return $this->render('Admin/profil.html.twig', [
            'page' => $page,
            'error' => $error ?? null,
            'success' => $success ?? null,
            'last_email' => $last_email ?? '',
        ]);
    }
}
