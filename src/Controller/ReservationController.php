<?php 

namespace App\Controller;

use App\Repository\ScheduleRepository;
use DateTime;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController 
{
    #[Route('/reservation', name:'reservation')]
    public function reservation(Request $request, ScheduleRepository $scheduleRepository)
    {

        if ($request->isMethod('POST')) {
            $date = $request->get('date');

            if ($date) {
                $date = date_create_from_format('D M d Y H:i:s e+',$date);
                $hour = date_format($date, 'H:i');
                $hour = new DateTime('1970-01-01 '.$hour);
                $timestamp = date_timestamp_get($hour);

                $day = date('N', date_timestamp_get($date));

                $scheduleDay = $scheduleRepository->findOneBy(['day' => $day]);
                $schedules_evening = [];
                $schedules_noon = [];

                //  Si le matin n'est pas fermé 
                //  Si l'heure de la date donnée est inférieure à la fin du service
                // Si l'heure de la date donnée est supérieure à l'heure du début de service
                //      -> + 15 min à time tant qu'inférieur à l'heure de la date donnée
                // Tant que time est inférieure à fin du service
                //      -> Ajouter $time aux horaires du service du midi 
                //      ->Ajout de 15 minutes
                if (!$scheduleDay->getEveningClose()) {   
                    // Evening
                    $evening_end = strtotime('-30 minutes', date_timestamp_get($scheduleDay->getEveningEnd()));
                    $evening_start = date_timestamp_get($scheduleDay->getEveningStart());                     
                    
                    $time = $evening_start;
                    
                    if ($timestamp < $evening_end) {
                        if ($timestamp > $evening_start){
                            while ($time <= $timestamp) {
                                $time = strtotime('+15 minutes', $time);
                            }
                        }
                        while ($time <= $evening_end) {
                            array_push($schedules_evening, $time);
                            $time = strtotime('+15 minutes', $time);
                        }
                    } 
                }
                if (!$scheduleDay->getNoonClose()) {
                    // Night
                    $noon_end = strtotime('-30 minutes', date_timestamp_get($scheduleDay->getNoonEnd()));
                    $noon_start = date_timestamp_get($scheduleDay->getNoonStart());
                    
                    $time = $noon_start;

                    if ($timestamp > $noon_start) {
                        while ($time <= $timestamp) {
                            $time = strtotime('15 minutes', $time);
                        }
                    }
                    while($time <= $noon_end) {
                        array_push($schedules_noon,$time);
                        $time = strtotime('+15 minutes', $time);
                    }
                }
                dump($schedules_evening);
                dump($schedules_noon);
                return new JsonResponse([
                    'evening' => $schedules_evening,
                    'noon' => $schedules_noon,
                ]);
            }
        }

        return $this->render('Reservation/reservation.html.twig', [
        ]);
    }
}