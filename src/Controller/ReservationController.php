<?php 

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\DateRepository;
use App\Repository\ReservationRepository;
use App\Repository\ScheduleRepository;
use App\Repository\TemplateRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
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
    private function addTime($time, $schedule, $timestamp, $array = []) {
        if ( $time == 'evening') {
            $end = strtotime('-30 minutes', date_timestamp_get($schedule->getEveningEnd()));
            $start = date_timestamp_get($schedule->getEveningStart());                     
        } else {
            $end = strtotime('-30 minutes', date_timestamp_get($schedule->getNoonEnd()));
            $start = date_timestamp_get($schedule->getNoonStart());     
        }
        
        $time = $start;
        
        if ($timestamp < $end) {
            if ($timestamp > $start){
                while ($time <= $timestamp) {
                    $time = strtotime('+15 minutes', $time);
                }
            }
            while ($time <= $end) {
                array_push($array, $time);
                $time = strtotime('+15 minutes', $time);
            }
        } 
        return $array;
    }
    private function getAndDisplaySchedulesSpecialDate($time, $schedule, $repository, $timestamp, $array= []) {
        $day = date('N', date_timestamp_get($schedule->getDate()));
                            
        $scheduleDay = $repository->findOneBy(['day' => $day]);
        if ($time == 'evening') {
            if (!$scheduleDay->getEveningClose()){
                return $array = $this->addTime($time, $scheduleDay, $timestamp);
            }
        } else {
            if (!$scheduleDay->getNoonClose()){
                return $array = $this->addTime($time, $scheduleDay, $timestamp);
            }
        }
    }

    #[Route('/reservation', name:'reservation')]
    public function reservation(Request $request, ScheduleRepository $scheduleRepository, 
        ReservationRepository $reservationRepository, UserRepository $userRepository, DateRepository $dateRepository,
        TemplateRepository $templateRepository)
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            $templates = $user->getTemplate();
        }
        if ($request->isMethod('POST')) {
            $date = $request->get('date');
            $reservation = json_decode($request->getContent(), true);
            $template = $request->get('template');

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
                            $schedules_evening = $this->getAndDisplaySchedulesSpecialDate('evening', $scheduleSpecialDate, $scheduleRepository, $timestamp);
                        } else {
                            $schedules_evening = $this->addTime('evening', $scheduleSpecialDate, $timestamp);
                        }
                    }
                    if (!$scheduleSpecialDate->getNoon_Close()) {
                        // Night
                        if ($scheduleSpecialDate->getNoon_normal()) {
                            $schedules_noon = $this->getAndDisplaySchedulesSpecialDate('noon', $scheduleSpecialDate, $scheduleRepository, $timestamp);
                        } else {
                            $schedules_noon = $this->addTime('noon', $scheduleSpecialDate, $timestamp);
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
                        $schedules_evening = $this->addTime('evening', $scheduleDay, $timestamp);
                    }
                    if (!$scheduleDay->getNoonClose()) {
                        // Night
                        $schedules_noon = $this->addTime('noon', $scheduleDay, $timestamp);
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
                        'result' => 'error_pattern'
                    ]);
                }
                $allergies = $reservation['allergies'];
                if (count($allergies) > 0) {
                    foreach($allergies as $allergie) {
                        if (!$this->checkPattern($allergie, '/^(gluten|fish|shellfish|eggs|peanuts|mustard|molluscs|soy|sulphites|sesame|celery|lupines|milk|nuts|)$/'))
                        {
                            return new JsonResponse([
                                'result' => 'error_pattern'
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
            if ($template) {
                try {
                    $template = $templateRepository->find($template);
                    dump($template);
                    foreach ($templates as $userTemplate) {
                        if ($template == $userTemplate) {
                            return new JsonResponse([
                                'name' => $template->getName(),
                                'places' => $template->getPlace(),
                                'allergies' => $template->getAllergies()
                            ]);
                        }
                    }
                    return new JsonResponse([
                        'result' => 'error'
                    ]);
                } catch (Exception $e) {
                    return new JsonResponse([
                        'result' => 'error'
                    ]);
                }
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

        dump($templates);

        return $this->render('Reservation/reservation.html.twig', [
            'error' => $error ?? null,
            'success' => $success ?? null,
            'user_templates' => $templates
        ]);
    }
}