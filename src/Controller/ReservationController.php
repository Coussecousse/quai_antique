<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController 
{
    #[Route('/reservation', name:'reservation')]
    public function reservation()
    {
        return $this->render('Reservation/reservation.html.twig', [
        ]);
    }
}