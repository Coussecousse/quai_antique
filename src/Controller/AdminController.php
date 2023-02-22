<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\Query\AST\NewObjectExpression;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\Security;
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

        if ($result == "success_email") {
            $success = "Email modifié avec succès !";
        } else if ($result == 'error_email_email') {
            $error = "Email invalide.";
        } else if ($result == 'error_invalid') {
            $error = "Mot de passe invalide.";
        }

        $last_email = $request->getSession()->get('last_email');
        
        if ($request->isMethod('POST')) {
            $em = $doctrine->getManager();

            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = $this->getUser();
            if ($email && $password) {
                //  Check  password
                if (!$userPasswordHasher->isPasswordValid($user, $password)){
                    $request->getSession()->set('last_email', $email);

                    $response  = new JsonResponse([
                        'result' => 'error_invalid'
                    ]);
                    return $response;
                } else {
                    // Check if another user exist
                    $other_user = $repository->findBy([
                        'email' => $email
                    ]);
                    if ($other_user != null) {
                        $response = new JsonResponse([
                            'result' => 'error_email',
                        ]);
                        return $response;
                    } else {
                        $user->setEmail($email);
                        $success = true;
        
                        $em->persist($user);
                        $em->flush();

                        $request->getSession()->remove('last_email');

                        $response = new JsonResponse([
                            'result' => 'success',
                        ]);
                        $success = "Email modifié avec succès !";
                        return $response;
                    }
                }    
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
