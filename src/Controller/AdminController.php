<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Food;
use App\Entity\Menu;
use App\Entity\Offer;
use App\Form\CardType;
use App\Form\ImageType;
use App\Form\MenusType;
use App\ImageOptimizer;
use App\Repository\CarouselRepository;
use App\Repository\FoodRepository;
use App\Repository\MenuRepository;
use App\Repository\OfferRepository;
use App\Repository\UserRepository;
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

class AdminController extends AbstractController
{
    private function checkPattern($expression, $pattern) 
    {
        return preg_match($pattern, $expression);
    }
    private function changeRestaurantDatas($restaurant, $id, $element, $path) {

        $restaurant = array_replace($restaurant, array($id => $element));
        $yaml = Yaml::dump($restaurant);
        
        file_put_contents($path, $yaml);

        return new JsonResponse([
            'result' => 'success',
        ]);
    }
    private function changeImageDatas($id, $value, $input, $repository) {
        $image = $repository->find($id);

        if ($value === 'title') {
            $image->setTitle($input);
        } else {
            $image->setDescription($input);
        }
        $repository->save($image, true);

        return new JsonResponse([
            'result' => 'success',
        ]);
    }
    private function deleteElement($id, $repository) {

        dump($id);
        $element = $repository->find($id);
        $repository->remove($element, true);

        return new JsonResponse([
            'result' => 'success',
        ]);
    }
    private function handleImages($imageName, $title, $description, $doctrine)
    {

        $sizes = ['small' => 420, 'medium' => 735,'large' => 950, 'extraLarge' => 1200];
        $path = $this->getParameter('uploads') .'/'. $imageName;
        $imageName = explode('.', $imageName);
        
        $em = $doctrine->getManager();
        
        $carousel = new Carousel();
        $carousel->setPath('/build/images/resize/'.$imageName[0])->setTitle($title)->setDescription($description);
        

        $em->persist($carousel);
        $em->flush();        
        foreach($sizes as $key => $size) {
            
            $newNameImageSize = $imageName;
            array_splice($newNameImageSize, -1, 0, '-'.$key.'.');
            $newNameImageSize = implode('', $newNameImageSize);
            $newPath = $this->getParameter('resize') .'/'.$newNameImageSize;

            try {
                copy($path, $newPath);
            } catch (Exception $e) {
                dump($e);
            }

            $optimizer = new ImageOptimizer();
            $optimizer->resize($newPath, $size);
            
        }
    }
    #[Route('admin/profil/{page_up}/{page_down}/{page_three}', name: "admin_card", methods: ['GET', 'POST'], defaults: ['page_up' => 'informations', 'page_down' => 'carousel'])]
    public function profilCard(string $page_up, string $page_down, string $page_three, Request $request, FoodRepository $foodRepository, MenuRepository $menuRepository, OfferRepository $offerRepository)
    {
        $path = $this->getParameter('kernel.project_dir') . '/config/data/restaurant.yaml';
        $restaurant = Yaml::parseFile($path);
        
        $form_card = $this->createForm(CardType::class);
        $form_card->handleRequest($request);

        if ($form_card->isSubmitted() && $form_card->isValid()) {
            $title = $form_card->get('title')->getData();
            $description = $form_card->get('description')->getData() ?? '';
            $price = $form_card->get('price')->getData();

            $food = new Food();
            $food->setTitle($title)->setDescription($description)->setprice($price);

            switch($page_three) {
                case 'entrées': 
                    $food->setCategory('starter');
                    break;
                case 'plats':
                    $food->setCategory('main');
                    break;
                case 'desserts':
                    $food->setCategory('dessert');
                    break;
            }
            $foodRepository->save($food, true);
            return $this->redirectToRoute('admin_card', [
                "page_up" => $page_up,
                'page_down' => $page_down,
                'page_three' => $page_three,
                'result' => "success"
            ]);
        } else if ($form_card->isSubmitted() && !$form_card->isValid()) {
            $request->query->remove('result');
        }
        
        $menu = new Menu();
        $offer = new Offer();
        $offer->setTitle('Offre 1')->setConditions('Condition 1')->setDescription(['Entrée', 'Plat']);
        $menu->addOffer($offer);
        
        $form_menu = $this->createForm(MenusType::class, $menu);
        $form_menu->handleRequest($request);
        
        if ($form_menu->isSubmitted() && $form_menu->isValid()) {
            // ... do your form processing, like saving the Task and Tag entities
            $menu = $form_menu->getData(); 
            $menuRepository->save($menu, true);

            return $this->redirectToRoute('admin_card', [
                "page_up" => $page_up,
                'page_down' => $page_down,
                'page_three' => $page_three,
                'result' => "success"
            ]);
        }  else if ($form_menu->isSubmitted() && !$form_menu->isValid()){
            $request->query->remove('result');
        }
        $id = $request->request->get('id');
        if ($request->isMethod('POST') && $id) {

            // Delete Food
            $delete = $request->request->get('delete');
            if ($delete) {
                return $this->deleteElement($id, $foodRepository);
            }
            // Delete Menu
            $deleteMenu = $request->request->get('deleteMenu');
            if ($deleteMenu) {
                return $this->deleteElement($id, $menuRepository);
            }

            $newTitle = $request->request->get('title');
            $newDescription = $request->request->get('description');
            $newPrice = $request->request->get('price');
            if (!$this->checkPattern($newTitle, "/^[\p{L}\d\s.\'’()-]+$/u") || !$this->checkPattern($newDescription, "/^[\p{L}\d\s.,()'’-]{0,255}$/u")) 
            {
                return new JsonResponse([
                    'result' => 'error_pattern'
                ]);
            }
            $newFood = $foodRepository->find($id);
            $newFood->setTitle($newTitle)->setDescription($newDescription)->setPrice($newPrice);
            $foodRepository->save($newFood, true);

            return new JsonResponse([
                'result' => 'success'
            ]);
            
        } 
        if ($request->isMethod('POST')) {
            
            $dataMenu = json_decode($request->getContent(), true);
            if (!is_array($dataMenu) || !$dataMenu) {
                return new JsonResponse([
                    'result' => 'error'
                ]);
            }
            $menuId = $dataMenu['id'];

            try {
                $modifyMenu = $menuRepository->find($menuId);
            } catch (Exception $e) {
                return new JsonResponse([
                    'result' => 'error'
                ]);
            }
            $menuTitle = $dataMenu['menuTitle'];
            if (!$this->checkPattern($menuTitle, "/^[\p{L}\d\s.\'’()-]+$/u")) 
            {
                return new JsonResponse([
                    'result' => 'error_pattern'
                ]);
            }
            $modifyMenu->setTitle($menuTitle);

            $offersToRemove = $modifyMenu->getOffers();
            foreach ($offersToRemove as $offer) {
                $modifyMenu->removeOffer($offer);
            }
            $newOffers = $dataMenu['offers'];

            foreach($newOffers as $newOffer) {
                $offer = new Offer();
                if (!$this->checkPattern($newOffer[0], "/^[\p{L}\d\s.\'’()-]+$/u")
                    || !$this->checkPattern($newOffer[1], "/^[\p{L}\d\s.\'’()-]+$/u")
                    || !$this->checkPattern($newOffer[3], "/^\d+(\.\d+)?\d$/"))
                {
                    return new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                }
                foreach($newOffer[2] as $food) {
                    if (!$this->checkPattern($food, "/^[\p{L}\d\s.\'’()-]+$/u")) {
                        return new JsonResponse([
                            'result' => 'error_pattern'
                        ]);
                    }
                }
                $offer->setTitle($newOffer[0])->setConditions($newOffer[1])->setDescription($newOffer[2])->setPrice($newOffer[3]);
                $modifyMenu->addOffer($offer);
            }
            $menuRepository->save($modifyMenu, true);

            return new JsonResponse([
                'result' => 'success'
            ]);
        }

        $result = $request->query->get('result');
        
        switch($result) {
            case 'success' : 
                $success = "Modification effectuée avec succès !";
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
        
        switch ($page_three) {
            case 'entrées':
                $values = $foodRepository->findBy(['category' => "starter"]);
                break;
            case 'plats':
                $values = $foodRepository->findBy(['category' => 'main']);
                break;
            case 'desserts':
                $values = $foodRepository->findBy(['category' => 'dessert']);
                break;                
            default: 
                $values = $foodRepository->findBy(['category' => "starter"]);
                break;
        }
        $menus = $menuRepository->findAll();

        return $this->render('Admin/profil_down/card/card.html.twig', [
            'page_up' => $page_up, 
            'page_down' => $page_down, 
            'page_three'=> $page_three,
            'form_card' => $form_card->createView(),
            'form_menu' => $form_menu,
            'error' => $error ?? null,
            'success' => $success ?? null,
            'last_email' => $last_email ?? '',
            'restaurant' => $restaurant,
            'values' => $values,
            'errors' => $errors ?? null,
            'menus' => $menus
        ]);
    }

    #[Route('admin/profil/{page_up}/{page_down}', name: "admin_profil", methods: ['GET', 'POST'], defaults: ['page_up' => 'informations', 'page_down' => 'carousel'])]
    public function profil(string $page_up, string $page_down, UserRepository $repository, Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger, CarouselRepository $carouselRepository)
    {
        $path = $this->getParameter('kernel.project_dir') . '/config/data/restaurant.yaml';
        $restaurant = Yaml::parseFile($path);

        $last_email = $request->getSession()->get('last_email');

        $form_image = $this->createForm(ImageType::class);
        $form_image->handleRequest($request);

        if ($form_image->isSubmitted() && $form_image->isValid() ){
            $image = $form_image->get('image')->getData();
            $originalFileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFileName);
            $newFileName = $safeFilename . '-' .uniqid() . '.' . $image->guessExtension();

            try {
                $image->move(
                    $this->getParameter('uploads'),
                    $newFileName
                );
            } catch (FileException $e) {
                dump($e);
            }

            $title = $form_image->get('title')->getData();
            $description = $form_image->get('description')->getData();
            $this->HandleImages($newFileName, $title, $description, $doctrine);
            return $this->redirectToRoute('admin_profil', [
                "page_up" => $page_up,
                'page_down' => $page_down,
                'result' => "success"
            ]);
        } else if ($form_image->isSubmitted() && !$form_image->isValid()) {
            $request->query->remove('result');
        }

        if ($request->isMethod('POST')) {

            $em = $doctrine->getManager();

            // Informations
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            // Restaurant
            $telRestaurant = $request->request->get('tel'); 
            $emailRestaurant = $request->request->get('emailRestaurant');
            $cityRestaurant =  $request->request->get('city');
            $streetRestaurant = $request->request->get('street');
            $postcodeRestaurant = $request->request->get('postcode');
            $placesRestaurant = $request->request->get('places');
            // Carousel
            $id = $request->request->get('id');
            $imageTitle = $request->request->get('imageTitle');
            $delete = $request->request->get('delete');
            $imageDescription = $request->request->get('imageDescription');

            $user = $this->getUser();
            // Change password
            if ($password && !$email) {
                $oldPassword = $request->request->get('oldPassword');
                // Check password pattern
                if (!$this->checkPattern($password, "/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,80}$/"))
                {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
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
                // Check email pattern
                //  https://www.php.net/manual/en/filter.filters.validate.php
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }

                //  Check same password
                if (!$userPasswordHasher->isPasswordValid($user, $password)) {
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
            // Change Restaurant
            //  Tel :
            if ($telRestaurant) {
                $telRestaurant = trim($telRestaurant, " -.+");
                if (!$this->checkPattern($telRestaurant, "/^(?:0|33)[1-9](?:\d{2}){4}$/")) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                } else if ($telRestaurant[0] == '3' && $telRestaurant[1] == '3') {
                    $telRestaurant = substr($telRestaurant, 2);
                }
                if (!str_starts_with($telRestaurant, "0")) {
                    $telRestaurant = '0'.$telRestaurant;
                }
                $telRestaurant = chunk_split($telRestaurant, 2, ' ');

                return $this->changeRestaurantDatas($restaurant, 'tel', $telRestaurant, $path);
            }
            //  Email
            if ($emailRestaurant) {
                if (!filter_var($emailRestaurant, FILTER_VALIDATE_EMAIL)) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeRestaurantDatas($restaurant, 'email', $emailRestaurant, $path);
            }
            //  City
            if ($cityRestaurant) {
                if (!$this->checkPattern($cityRestaurant, '/^[\p{L}\s\'-]{2,50}$/u')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeRestaurantDatas($restaurant, 'city', $cityRestaurant, $path);
            }
            //  Street
            if ($streetRestaurant) {
                if (!$this->checkPattern($streetRestaurant, '/^[\p{L}\d\s.\'’-]{5,80}$/u')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeRestaurantDatas($restaurant, 'street', $streetRestaurant, $path);
            }
            //  Post Code
            if ($postcodeRestaurant || $postcodeRestaurant == '0') {
                if (!$this->checkPattern($postcodeRestaurant, '/^\d{5}$/')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeRestaurantDatas($restaurant, 'postcode', $postcodeRestaurant, $path);
            }
            //  Places
            if ($placesRestaurant) {
                if (!$this->checkPattern($placesRestaurant, '/^\d{1,3}$/')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeRestaurantDatas($restaurant, 'places', $placesRestaurant, $path);
            }

            // Carousel
            // Title
            if ($imageTitle) {
                if (!$this->checkPattern($imageTitle, '/^[\p{L}\d\s.\'’-]{2,50}$/u')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeImageDatas($id, "title", $imageTitle, $carouselRepository);
            }
            if ($delete) {
                return $this->deleteElement($id, $carouselRepository);
            }
            if ($imageDescription) {
                if (!$this->checkPattern($imageDescription, '/^[\p{L}\d\s.\'’-]{10,255}$/u')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeImageDatas($id, "description", $imageDescription, $carouselRepository);
            }
        }

        $imagesCarousel = $carouselRepository->findAll();

        $result = $request->query->get('result');

        switch($result) {
            case 'success' : 
                $success = "Modification effectuée avec succès !";
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

        return $this->render('Admin/profil.html.twig', [
            'images_carousel' => $imagesCarousel,
            'page_up' => $page_up,
            'page_down' => $page_down,
            'page_three' => null,
            'form_image' => $form_image->createView(),
            'error' => $error ?? null,
            'success' => $success ?? null,
            'last_email' => $last_email ?? '',
            'restaurant' => $restaurant
        ]);
    }

}
