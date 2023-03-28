<?php 

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\DateRepository;
use App\Repository\ReservationRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use DateTime;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController 
{
    private function checkPattern($expression, $pattern) 
    {
        return preg_match($pattern, $expression);
    }

    #[Route('/reservation', name:'reservation')]
    public function reservation(Request $request, ScheduleRepository $scheduleRepository, 
        ReservationRepository $reservationRepository, UserRepository $userRepository, DateRepository $dateRepository)
    {

        if ($request->isMethod('POST')) {
            $date = $request->get('date');
            $reservation = json_decode($request->getContent(), true);

            if ($date) {
                $date = date_create_from_format('D M d Y H:i:s e+',$date);
                $hour = date_format($date, 'H:i');
                $hour = new DateTime('1970-01-01 '.$hour);
                $timestamp = date_timestamp_get($hour);

                $day = date('N', date_timestamp_get($date));
                
                $schedules_evening = [];
                $schedules_noon = [];

                // Special Date
                $scheduleSpecialDate = $dateRepository->findOneBy(['date' => $date]);
                if ($scheduleSpecialDate) {
                    if (!$scheduleSpecialDate->getEvening_Close()) {   
                        // Evening
                        if ($scheduleSpecialDate->getEvening_normal()) {
                            $daySpecialDate = date('N', date_timestamp_get($scheduleSpecialDate->getDate()));
                            
                            $scheduleDay = $scheduleRepository->findOneBy(['day' => $daySpecialDate]);
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
                        } else {
                            $evening_end = strtotime('-30 minutes', date_timestamp_get($scheduleSpecialDate->getEveningEnd()));
                            $evening_start = date_timestamp_get($scheduleSpecialDate->getEveningStart());                     
                            
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
                    }
                    if (!$scheduleSpecialDate->getNoon_Close()) {
                        // Night
                        if ($scheduleSpecialDate->getNoon_normal()) {
                            $daySpecialDate = date('N', date_timestamp_get($scheduleSpecialDate->getDate()));
                            
                            $scheduleDay = $scheduleRepository->findOneBy(['day' => $daySpecialDate]);
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
                        } else {
                            $noon_end = strtotime('-30 minutes', date_timestamp_get($scheduleSpecialDate->getNoonEnd()));
                            $noon_start = date_timestamp_get($scheduleSpecialDate->getNoonStart());
                            
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
                    }
                } else {
                    $scheduleDay = $scheduleRepository->findOneBy(['day' => $day]);
                    //  Si le matin n'est pas fermé 
                    //  Si l'heure de la date donnée est inférieure à la fin du service
                    //  Si l'heure de la date donnée est supérieure à l'heure du début de service
                    //      -> + 15 min à time tant qu'inférieur à l'heure de la date donnée
                    //  Tant que time est inférieure à fin du service
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
                }
                
                return new JsonResponse([
                    'evening' => $schedules_evening,
                    'noon' => $schedules_noon,
                ]);
            }
            if ($reservation) {
                $name = $reservation['name'];
                $places = $reservation['places'];
                $date = $reservation['date'];
                $schedule = $reservation['schedule'];

                if (!$this->checkPattern($name, '/^[\p{L}\d\s.\'’()-]{2,100}+$/u') ||
                    !$this->checkPattern($places, '/^\d+$/') || 
                    !$this->checkPattern($date, '/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/') ||
                    !$this->checkPattern($schedule, '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/')
                )
                {
                    return new JsonResponse([
                        'result' => 'error_patterntest'
                    ]);
                }
                $allergies = $reservation['allergies'];
                if (count($allergies) > 0) {
                    foreach($allergies as $allergie) {
                        if (!$this->checkPattern($allergie, '/^(gluten|fish|shellfish|eggs|peanuts|mustard|molluscs|soy|sulphites|sesame|celery|lupines|milk|nuts|)$/'))
                        {
                            return new JsonResponse([
                                'result' => 'error_patterntest2'
                            ]);
                        }
                    }
                }
                
                // Test date 
                // Special date 
                $scheduleSpecialDate = $dateRepository->findOneBy(['date' => new Datetime($date)]);
                $fictiveHour = new Datetime('1970-01-01 '.$schedule);
                if ($scheduleSpecialDate) {
                    if ($scheduleSpecialDate->getEvening_close()){
                        if ($scheduleSpecialDate->getNoon_normal()) {
                            $daySpecialDate = date('N', date_timestamp_get($scheduleSpecialDate->getDate()));
                            $scheduleDay = $scheduleRepository->findOneBy(['day' => $daySpecialDate]);
                            
                            if (!$scheduleDay->getNoonClose()) {
                                if ($fictiveHour < $scheduleDay->getNoonStart()){
                                    return new JsonResponse([
                                        'result' => 'error'
                                    ]);
                                }
                            } else {
                                return new JsonResponse([
                                    'result' => 'error'
                                ]) ;
                            }
                        } else {
                            if (!$scheduleSpecialDate->getNoon_close()) {
                                if ($fictiveHour < $scheduleSpecialDate->getNoonStart()) {
                                    return new JsonResponse([
                                        'result' => 'error'
                                    ]);
                                }
                            } else {
                                return new JsonResponse([
                                    'result' => 'error'
                                ]) ;
                            }
                        }
                    } else if ($scheduleSpecialDate->getNoon_close()) {
                        if ($scheduleSpecialDate->getEvening_normal()) {
                            $daySpecialDate = date('N', date_timestamp_get($scheduleSpecialDate->getDate()));
                            $scheduleDay = $scheduleRepository->findOneBy(['day' => $daySpecialDate]);
                            if ($fictiveHour > $scheduleDay->getEveningEnd()) {
                                return new JsonResponse([
                                    'result' => 'error'
                                ]);
                            }
                        } else {
                            if ($fictiveHour > $scheduleSpecialDate->getEveningEnd()) {
                                return new JsonResponse([
                                    'result' => 'error'
                                ]);
                            }
                        }
                    }
                } else {
                    $day = date('N', strtotime($date));
                    $dayBDD = $scheduleRepository->findOneBy(['day'=> $day]);
                    // Si le midi est fermé
                    //      Si le soir n'est pas fermé
                    //          Check si l'heure est avant le début du service du soir et si oui alors il y a erreur
                    //      Sinon ça veut dire que le soir est égalment fermé donc il y a une erreur
                    // Sinon si le soir est fermé
                    //      Si l'heure est après la fin du service du midi 
                    //          Alors erreur
                    if ($dayBDD->getEveningClose()) {
                        if (!$dayBDD->getNoonClose()) {
                            if ($fictiveHour < $dayBDD->getNoonStart()) {
                                return new JsonResponse([
                                    'result' => 'error'
                                ]);
                            }
                        } else {
                            return new JsonResponse([
                                'result' => 'error'
                            ]) ;
                        }
                    } else if ($dayBDD->getNoonClose()) {
                        if ($fictiveHour > $dayBDD->getEveningEnd()) {
                            return new JsonResponse([
                                'result' => 'error'
                            ]);
                        }
                    }
                }

                if ($this->getUser()) {
                    $user = $userRepository->find($this->getUser());
                    if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                        dump($date);
                        if ($reservationRepository->findByDate(new Datetime($date), $user))  {
                            return new JsonResponse([
                                'result' => 'error_date_exist'
                            ]);
                        }
                    }
                }
                $date = new DateTime($date.' '.$schedule);
                $new_res = new Reservation();
                $new_res->setName($name)->setPlaces($places)->setDate($date)->setAllergies($allergies);
                if ($this->getUser()) {
                    $new_res->setClient($userRepository->find($this->getUser()));
                }
                $reservationRepository->save($new_res, true);

                return new JsonResponse([
                    'result' => 'success'
                ]);
            }
        }

        $result = $request->query->get('result');

        switch($result) {
            case 'success' : 
                $success = "Réservation enregistrée avec succès !";
                break;
            case 'error_pattern':
                $error = "L'entrée fournie ne correspond pas au format requis.";
                break;
            case 'error' : 
                $error = "Un problème est survenu. Veuillez nous excuser pour la gêne occasionnée. Si le problème persiste, n'hésitez pas à nous contacter directement.";
                break;
            case 'error_date_exist':
                $error = "Vous avez déjà réservé chez nous à cette date.";
                break;
            default : 
                break;
        }


        return $this->render('Reservation/reservation.html.twig', [
            'error' => $error ?? null,
            'success' => $success ?? null,
        ]);
    }
}