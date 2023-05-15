<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Entity\Date;
use App\Entity\Food;
use App\Entity\Menu;
use App\Entity\Offer;
use App\Form\CardType;
use App\Form\DatesType;
use App\Form\ImageType;
use App\Form\MenusType;
use App\ImageOptimizer;
use App\Repository\CarouselRepository;
use App\Repository\DateRepository;
use App\Repository\FoodRepository;
use App\Repository\MenuRepository;
use App\Repository\OfferRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
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
    private function deleteImage($id, $repository) {

        $element = $repository->find($id);
        // in Build
        $path = $element->getPath();

        $path = explode('/', $path); // path = array['path', 'to', 'element']
        $name = array_splice($path, -1); // name = array['name.jpg']
        $name = implode('', $name); // name = name.jpg
        $name = explode('.', $name); // name = array['name','jpg']

        $sizes = ['-large.', '-extraLarge.', '-medium.', '-small.'];

        try {
            foreach ($sizes as $size) {
                if (file_exists($this->getParameter('build-resize').$name[0].$size.$name[1]))
                {
                    unlink($this->getParameter('build-resize').$name[0].$size.$name[1]);
                }
                if (file_exists($this->getParameter('resize').$name[0].$size.$name[1]))
                {
                    unlink($this->getParameter('resize').$name[0].$size.$name[1]);
                }
            }

            if (file_exists($this->getParameter('uploads').implode('.', $name)))
            {
                unlink($this->getParameter('uploads').implode('.', $name));
            }
            if (file_exists($this->getParameter('build-uploads').implode('.', $name)))
            {
                unlink($this->getParameter('build-uploads').implode('.', $name));
            }

            $repository->remove($element, true);
        } catch (Exception $e) {
            return new JsonResponse([
                "result" => "error"
            ]);
        }


        return new JsonResponse([
            'result' => 'success',
        ]);
    }
    private function handleImages($imageName, $title, $description, $doctrine)
    {
        $sizes = ['small' => 420, 'medium' => 735,'large' => 950, 'extraLarge' => 1200];
        $path = $this->getParameter('uploads') .'/'. $imageName;

        $em = $doctrine->getManager();

        $carousel = new Carousel();
        $carousel->setPath('/images/resize/'.$imageName)->setTitle($title)->setDescription($description);

        $imageName = explode('.', $imageName);

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
                return false
            }

            $optimizer = new ImageOptimizer();
            $optimizer->resize($newPath, $size);
        }
        return true;
    }
    private function getErrorScheduleDate($form, $service, $newDate) {
        $time_close = $form->get($service. '_close')->getData();
        $time_normal = $form->get($service. '_normal')->getData();

        if ($time_close && $time_normal) {
            return false;
        }

        $time_start = $form->get($service. '_start')->getData();
        $time_end = $form->get($service. '_end')->getData();

        if (!$time_start || !$time_end) {
            if ($time_close || $time_normal) {
                return true;
            }
            return false;
        }
        return true;

    }
    private function setNewDate($newDate, $form, $service) {
        $time_close = $form->get($service. '_close')->getData();
        $time_normal = $form->get($service. '_normal')->getData();
        if ($time_close) {
            if ($service == 'evening'){
                $newDate->setEvening_close(true)->setEveningStart(null)->setEveningEnd(null)->setEvening_normal(false);
            } else {
                $newDate->setNoon_close(true)->setNoonStart(null)->setNoonEnd(null)->setNoon_normal(false);
            }
            return $newDate;
        }
        if ($time_normal) {
            if ($service == 'evening'){
                $newDate->setEvening_normal(true)->setEveningStart(null)->setEveningEnd(null)->setEvening_close(false);
            } else {
                $newDate->setNoon_normal(true)->setNoonStart(null)->setNoonEnd(null)->setNoon_close(false);
            }
            return $newDate;
        }

        $time_start = $form->get($service. '_start')->getData();
        $time_end = $form->get($service. '_end')->getData();

        if ($service == 'evening'){
            $newDate->setEveningStart($time_start->setTimezone(new DateTimeZone('Europe/paris')))
                    ->setEveningEnd($time_end->setTimezone(new DateTimeZone('Europe/paris')))
                    ->setEvening_close(false)->setEvening_normal(false);
        } else {
            $newDate->setNoonStart($time_start->setTimezone(new DateTimeZone('Europe/paris')))
                    ->setNoonEnd($time_end->setTimezone(new DateTimeZone('Europe/paris')))
                    ->setNoon_close(false)->setNoon_normal(false);
        }
        return $newDate;

    }

    function handleCardForm($form, $repository, $page) {
        try {
            $title = $form->get('title')->getData();
            $description = $form->get('description')->getData() ?? '';
            $price = $form->get('price')->getData();

            $food = new Food();
            $food->setTitle($title)->setDescription($description)->setprice($price);

            switch($page) {
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
            $repository->save($food, true);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    private function deleteElement($id, $repository) {
        try {
            $element = $repository->find($id);
            $repository->remove($element, true);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    private function setFood($request, $id, $repository) {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $price = $request->request->get('price');
        if (!$this->checkPattern($title, "/^[\p{L}\d\s.\'’()-]+$/u")
            || !$this->checkPattern($description, "/^[\p{L}\d\s.,()'’-]{0,255}$/u"))
        {
            return 'error_pattern';
        }
        try {
            $food = $repository->find($id);
            $food->setTitle($title)->setDescription($description)->setPrice($price);
            $repository->save($food, true);
        } catch (Exception $e) {
            return 'error';
        }
        return 'success';
    }
    private function handleMenu($request, $repository) {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data) || !$data) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }

        $id = $data['id'];
        $title = $data['menuTitle'];
        $menu = $repository->find($id);
        if (!$menu) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }
        if (!$this->checkPattern($title, "/^[\p{L}\d\s.\'’()-]+$/u")) {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        }
        $menu->setTitle($title);

        $pastOffers = $menu->getOffers();
        foreach ($pastOffers as $offer) {
            $menu->removeOffer($offer);
        }
        $newOffers = $data['offers'];
        foreach ($newOffers as $newOffer) {
            $offer = new Offer();
            if (!$this->checkPattern($newOffer[0], "/^[\p{L}\d\s.\'’()-]+$/u")
                || !$this->checkPattern($newOffer[1], "/^[\p{L}\d\s.\'’()-]*$/u")) {
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
            $menu->addOffer($offer);
        }

        $repository->save($menu, true);
        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    private function handleFormDates($form, $repository) {
        $date = $form->get('date')->getData();
        $dateExist = $repository->findBy(['date' => $date]);
        if ($dateExist) {
            return 'error_date_already_exist';
        }

        $newDate = new Date();
        if (!$this->getErrorScheduleDate($form, 'evening', $repository)
            || !$this->getErrorScheduleDate($form, 'noon', $repository)) {
            return 'error_pattern';
        } else {
            $newDate->setDate($date);
            $newDate = $this->setNewDate($newDate, $form, 'evening');
            $newDate = $this->setNewDate($newDate, $form, 'noon');
            $repository->save($newDate, true);
            return 'success';
        }
    }
    private function handleFormImage($form, $slugger, $doctrine ) {
        $image = $form->get('image')->getData();
        $originalFileName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFileName);
        $newFileName = $safeFilename . '-' .uniqid() . '.' . $image->guessExtension();

        try {
            $image->move(
                $this->getParameter('uploads'),
                $newFileName
            );
        } catch (FileException $e) {
            return 'error';
        }

        $title = $form->get('title')->getData();
        $description = $form->get('description')->getData();
        if (!$this->HandleImages($newFileName, $title, $description, $doctrine)) {
            return 'error';
        }
        return 'success';
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
        
        //Check password pattern
        if (!$this->checkPattern($password, "/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,80}$/")) {
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
    private function handleTel($telRestaurant, $restaurant, $path) {
        $telRestaurant = trim($telRestaurant, " -.+");
        if (!$this->checkPattern($telRestaurant, "/^(?:0|33)[1-9](?:\d{2}){4}$/")) {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        } else if ($telRestaurant[0] == '3' && $telRestaurant[1] == '3') {
            $telRestaurant = substr($telRestaurant, 2);
        }
        if (!str_starts_with($telRestaurant, "0")) {
            $telRestaurant = '0'.$telRestaurant;
        }
        $telRestaurant = chunk_split($telRestaurant, 2, ' ');

        return $this->changeRestaurantDatas($restaurant, 'tel', $telRestaurant, $path);
    }
    private function handleEmailRestaurant($email, $restaurant, $path) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        }
        return $this->changeRestaurantDatas($restaurant, 'email', $email, $path);
    }
    private function handleSomeRestaurantDatas($data, $dataName, $restaurant, $path) {
        if (!filter_var($data, FILTER_VALIDATE_INT)) {
            return new JsonResponse([
                'result' => 'error_pattern'
            ]);
        }
        return $this->changeRestaurantDatas($restaurant, $dataName , $data, $path);
    }
    private function handleSchedules($id, $repository, $schedule_evening, $schedule_noon) {
        if (!$this->checkPattern($id, "/^\d+$/" )) {
            return 'error';
        }
        try {
            $day = $repository->find($id);
        } catch (Exception $e) {
            return 'error';
        }
        if (count($schedule_evening) == 3) {
            $close = $schedule_evening['close'];
            if ($close != 'on') {
                return 'error';
            }
            try {
                $day->setEveningClose(true)
                ->setEveningStart(null)
                ->setEveningEnd(null);
            } catch (Exception $e) {
                return 'error';
            }
        } else if (count($schedule_evening) == 2) {
            foreach($schedule_evening as $time) {
                if (!$this->checkPattern($time, '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/')) {
                    return 'error_pattern';
                };
            }
            $start = new DateTime($schedule_evening['start'], new DateTimeZone('Europe/Paris'));
            $end = new Datetime($schedule_evening['end'], new DateTimeZone('Europe/Paris'));
            try {
                $day->setEveningStart($start)
                    ->setEveningEnd($end)
                    ->setEveningClose(false);
            } catch (Exception $e) {
                return 'error';
            }
        }
        if (count($schedule_noon) == 3) {
            $close = $schedule_noon['close'];
            if ($close != 'on') {
                return 'error';
            }
            try {
                $day->setNoonClose(true)
                ->setNoonStart(null)
                ->setNoonEnd(null);
            } catch (Exception $e) {
                return 'error';
            }
        } else if (count($schedule_noon) == 2) {
            foreach($schedule_noon as $time) {
                if (!$this->checkPattern($time, '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/')) {
                    return 'error_pattern';
                };
            }
            $start = new DateTime($schedule_noon['start'], new \DateTimeZone('Europe/Paris'));
            $end = new DateTime($schedule_noon['end'], new \DateTimeZone('Europe/Paris'));
            try {
                $day->setNoonStart($start)
                    ->setNoonEnd($end)
                    ->setNoonClose(false);
            } catch (Exception $e) {
                return 'error';
            }
        }
        $repository->save($day, true);
        return 'success';
    }
    private function handleAllSchedules($scheduleAllDays, $repository) {
        foreach ($scheduleAllDays as $day) {
            $newDay = $repository->find($day['id']);
            foreach ($day['service'] as $key => $service) {
                if (count($service) == 2) {
                    foreach($service as $time) {
                        if (!$this->checkPattern($time, '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/')){
                            return new JsonResponse([
                                'result' => 'error_pattern'
                            ]);
                        }
                    }
                    $start = new DateTime($service[0], new DateTimeZone ('Europe/Paris'));
                    $end = new DateTime($service[1], new DateTimeZone('Europe/Paris'));
                    
                    if ($key == 0)  {
                        $newDay->setEveningStart($start)->setEveningEnd($end)->setEveningClose(false);
                    } else {
                        $newDay->setNoonStart($start)->setNoonEnd($end)->setNoonClose(false);
                    }
                } else {
                    if ($key == 0) {
                        $newDay->setEveningStart(null)->setEveningEnd(null)->setEveningClose(true);
                    } else {
                        $newDay->setNoonStart(null)->setNoonEnd(null)->setNoonClose(true);
                    }
                }
            }
            try {
                $repository->save($newDay, true);
            } catch (Exception $e) {
                return new JsonResponse([
                    'result' => 'error'
                ]);
            }
        }
        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    private function handleDeleteDate($request, $repository) {
        $id = $request->request->get('id_date');
        try {
            $date = $repository->find($id);
            $repository->remove($date, true);
        } catch (Exception $e) {
            return new JsonResponse([
                "result" => 'error'
            ]);
        }

        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    private function handleDeletePastDates($repository) {
        date_default_timezone_set('Europe/Paris');
        $today = date("Y-m-d", time());
        $today = new DateTime($today);
        try {
            $pastDates = $repository->findPastDates($today);
            foreach($pastDates as $pastDate) {
                $repository->remove($pastDate, true);
            }
        } catch(Exception $e) {
            return new JsonResponse([
                'result' => 'error'
            ]);
        }
        return new JsonResponse([
            'result' => 'success'
        ]);
    }
    #[Route('admin/profil/{page_up}/{page_down}/{page_three}', name: "admin_card", methods: ['GET', 'POST'], defaults: ['page_up' => 'informations', 'page_down' => 'carousel'])]
    public function profilCard(string $page_up, string $page_down, string $page_three, Request $request, FoodRepository $foodRepository, MenuRepository $menuRepository, OfferRepository $offerRepository)
    {
        // Get data from restaurant.yaml file
        $path = $this->getParameter('kernel.project_dir') . '/config/data/restaurant.yaml';
        $restaurant = Yaml::parseFile($path);

        $form_card = $this->createForm(CardType::class);
        $form_card->handleRequest($request);

        if ($form_card->isSubmitted() && $form_card->isValid()) {
            if ($this->handleCardForm($form_card, $foodRepository, $page_three)) {
                return $this->redirectToRoute('admin_card', [
                    "page_up" => $page_up,
                    'page_down' => $page_down,
                    'page_three' => $page_three,
                    'result' => "success"
                ]);
            } else {
                return $this->redirectToRoute('admin_card', [
                    "page_up" => $page_up,
                    'page_down' => $page_down,
                    'page_three' => $page_three,
                    'result' => "error"
                ]);
            }
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

            try {
                $menu = $form_menu->getData();
                $menuRepository->save($menu, true);
            } catch (Exception $e) {
                return $this->redirectToRoute('admin_card', [
                    "page_up" => $page_up,
                    'page_down' => $page_down,
                    'page_three' => $page_three,
                    'result' => "error"
                ]);
            }

            return $this->redirectToRoute('admin_card', [
                "page_up" => $page_up,
                'page_down' => $page_down,
                'page_three' => $page_three,
                'result' => "success"
            ]);
        }  else if ($form_menu->isSubmitted() && !$form_menu->isValid()){
            $request->query->remove('result');
        }

        $id_food = $request->request->get('id_food');
        $id_menu = $request->request->get('id_menu');
        if ($request->isMethod('POST') && ($id_food || $id_menu)) {

            // Delete Food
            $delete = $request->request->get('delete');
            if ($delete) {
                if ($this->deleteElement($id_food, $foodRepository)) {
                    return new JsonResponse([
                        'result' => 'success'
                    ]);
                } else {
                    return new JsonResponse([
                        'result' => 'error'
                    ]);
                }
            }
            // Delete Menu
            $deleteMenu = $request->request->get('deleteMenu');
            if ($deleteMenu) {
                if ($this->deleteElement($id_menu, $menuRepository)) {
                    return new JsonResponse([
                        'result' => 'success'
                    ]);
                } else {
                    return new JsonResponse([
                        'result' => 'error'
                    ]);
                }
            }

            if ($this->setFood($request, $id_food, $foodRepository) == 'success'){
                return new JsonResponse([
                    'result' => 'success'
                ]);
            } else if ($this->setFood($request, $id_food, $foodRepository) == 'error_pattern') {
                return new JsonResponse([
                    'result' => 'error_pattern'
                ]);
            } else {
                return new JsonResponse([
                    'result' => 'error'
                ]);
            }

        }
        if ($request->isMethod('POST') && !$form_card->isSubmitted() && !$form_menu->isSubmitted()) {
            return $this->handleMenu($request, $menuRepository);
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
    public function profil(string $page_up, string $page_down, UserRepository $repository, Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger, CarouselRepository $carouselRepository, ScheduleRepository $scheduleRepository, DateRepository $dateRepository)
    {
        // Informations
        $path = $this->getParameter('kernel.project_dir') . '/config/data/restaurant.yaml';
        $restaurant = Yaml::parseFile($path);
        // Last email use when try to to modify informations
        $last_email = $request->getSession()->get('last_email');

        // Schedules
        $schedules = $scheduleRepository->findAll();

        // Dates
        $form_dates = $this->createForm(DatesType::class);
        $form_dates->handleRequest($request);
        $special_dates = $dateRepository->findAllByDate();
        if ($form_dates->isSubmitted() && $form_dates->isValid()) {
            if ($this->handleFormDates($form_dates, $dateRepository) == 'success'){
                return $this->redirectToRoute('admin_profil', [
                    'page_up' => $page_up,
                    'page_down' => $page_down,
                    'result' => 'success'
                ]);
            } else if (($this->handleFormDates($form_dates, $dateRepository) == 'error_pattern')) {
                return $this->redirectToRoute('admin_profil', [
                    'page_up' => $page_up,
                    'page_down' => $page_down,
                    'result' => 'error_pattern'
                ]);
            } else {
                return $this->redirectToRoute('admin_profil', [
                    'page_up' => $page_up,
                    'page_down' => $page_down,
                    'result' => 'error_date_already_exist'
                ]);
            }
        }

        // Dates_search
        $search_date = $request->query->get('search_date');
        if ($search_date) {
            $dates = $dateRepository->findAfterDate(new DateTime($search_date));

            if (count($dates) == 0) {
                return $this->redirectToRoute('admin_profil', [
                    "page_up" => $page_up,
                    'page_down' => $page_down,
                    'result' => "error_no_date"
                ]);
            }
            $special_dates = $dates;
        }

        // Image
        $form_image = $this->createForm(ImageType::class);
        $form_image->handleRequest($request);
        if ($form_image->isSubmitted() && $form_image->isValid() ){
            if ($this->handleFormImage($form_image, $slugger, $doctrine) == 'success') {
                return $this->redirectToRoute('admin_profil', [
                    'page_up' => $page_up,
                    'page_down' => $page_down,
                    'result' => 'success'
                ]);
            } else {
                return $this->redirectToRoute('admin_profil', [
                    'page_up' => $page_up,
                    'page_down' => $page_down,
                    'result' => 'error'
                ]);
            }
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
            $id_image = $request->request->get('id_carousel');
            $imageTitle = $request->request->get('imageTitle');
            $delete = $request->request->get('delete');
            $imageDescription = $request->request->get('imageDescription');

            // Schedules
            $schedule_id = $request->request->all()['schedule']['id_schedule'] ?? null;
            $schedule_evening = $request->request->all()['schedule_evening'] ?? null;
            $schedule_noon = $request->request->all()['schedule_noon'] ?? null;
            $schedulesAllDays = json_decode($request->getContent(), true)['schedules'] ?? null;

            // Dates
            $delete_date = $request->request->get('delete_date');
            $delete_pastDates = $request->request->get('delete_pastDates');

            $user = $this->getUser();
            // Change password
            if ($password && !$email) {
                return $this->handlePassword($request, $password, $userPasswordHasher, $user, $em);
            }
            // Change email
            if ($email && $password) {
                return $this->handleEmail($email, $password, $userPasswordHasher, $user, $request, $repository);
            }
            // Change Restaurant
            //  Tel :
            if ($telRestaurant) {
                return $this->handleTel($telRestaurant, $restaurant, $path);
            }
            //  Email
            if ($emailRestaurant) {
                return $this->handleEmailRestaurant($emailRestaurant, $restaurant, $path);
            }
            //  City
            if ($cityRestaurant) {
                return $this->handleSomeRestaurantDatas($cityRestaurant, 'city', $restaurant, $path);
            }
            //  Street
            if ($streetRestaurant) {
                return $this->handleSomeRestaurantDatas($cityRestaurant, 'street', $restaurant, $path);
            }
            //  Post Code
            if ($postcodeRestaurant || $postcodeRestaurant == '0') {
                return $this->handleSomeRestaurantDatas($cityRestaurant, 'postcode', $restaurant, $path);
            }
            //  Places
            if ($placesRestaurant) {
                return $this->handleSomeRestaurantDatas($placesRestaurant, 'places', $restaurant, $path);
            }
            // Carousel
            // Title
            if ($imageTitle) {
                if (!$this->checkPattern($imageTitle, '/^[\p{L}\d\s.\'’-]{2,50}$/u')) {
                    return new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                }
                return $this->changeImageDatas($id_image, "title", $imageTitle, $carouselRepository);
            }
            if ($delete) {
                return $this->deleteImage($id_image, $carouselRepository);
            }
            if ($imageDescription) {
                if (!$this->checkPattern($imageDescription, '/^[\p{L}\d\s.\'’-]{10,255}$/u')) {
                    $response = new JsonResponse([
                        'result' => 'error_pattern'
                    ]);
                    return $response;
                }
                return $this->changeImageDatas($id_image, "description", $imageDescription, $carouselRepository);
            }
            // Schedules
            if ($schedule_id) {
                if ($this->handleSchedules($schedule_id, $scheduleRepository, $schedule_evening, $schedule_noon) == 'success') {
                    return $this->redirectToRoute('admin_profil', [
                        'page_up' => $page_up,
                        'page_down' => $page_down,
                        'result' => 'success'
                    ]);
                } else if ($this->handleSchedules($schedule_id, $scheduleRepository, $schedule_evening, $schedule_noon) == 'error_pattern') {
                    return $this->redirectToRoute('admin_profil', [
                        'page_up' => $page_up,
                        'page_down' => $page_down,
                        'result' => 'error_pattern'
                    ]);
                } else {
                    return $this->redirectToRoute('admin_profil', [
                        'page_up' => $page_up,
                        'page_down' => $page_down,
                        'result' => 'error'
                    ]);
                }
            }
            if ($schedulesAllDays) {
                return $this->handleAllSchedules($schedulesAllDays, $scheduleRepository);
            }

            // Dates
            if ($delete_date) {
                return $this->handleDeleteDate($request, $dateRepository);
            }
            if ($delete_pastDates) {
                return $this->handleDeletePastDates($dateRepository);
            }
        }
        $imagesCarousel = $carouselRepository->findAll();

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

        return $this->render('Admin/profil.html.twig', [
            'images_carousel' => $imagesCarousel,
            'page_up' => $page_up,
            'page_down' => $page_down,
            'page_three' => null,
            'form_image' => $form_image->createView(),
            'error' => $error ?? null,
            'success' => $success ?? null,
            'last_email' => $last_email ?? '',
            'restaurant' => $restaurant,
            'schedules' => $schedules,
            'form_dates' => $form_dates->createView(),
            'special_dates' => $special_dates,
        ]);
    }

}
