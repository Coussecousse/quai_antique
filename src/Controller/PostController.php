<?php

namespace App\Controller;

use App\Entity\Informations;
use App\Form\InformationsType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    #[Route('/reserver', name:'booking')]
    public function booking(Request $request, ) {
        
        $informations = new Informations();
        $formInformations = $this->createForm(InformationsType::class, $informations);

        // $formData = $request->request->all();
        // var_dump($formData);
        // // $informations->setName($formData['informations[name]']);
        // // $informations->setPerson($formData['person']);
        // // $informations->setAllergies($formData['allergies']);
        // // $informations->setAllergiesOther($formData['allergiesOther']);

        // // $validator = $this->get('validator');
        // // $errors = $validator->validate($informations);

        // // if (count($errors) > 0) {
        // //     return $this->render('Booking/Informations/informations.html.twig', [
        // //         'informations' => $formInformations,
        // //         'errors' => $errors,
        // //     ]);
        // // } else {
        // //     echo "validate";
        // // }


        // // $formInformations->handleRequest($request);
        // // if ($request->isXmlHttpRequest()){
        // //     print_r($request);
        // // } else {
        // //     echo 'no XmlHttpRequest made';
        // // }   
        // // if ($formInformations->isSubmitted() && $formInformations->isValid()) {
        // //     $places = $formInformations->get('person');

        // //     return true;
        // // }


        return $this->render('Booking/Informations/informations.html.twig', [
            'informations' => $formInformations,
        ]);
    }
}