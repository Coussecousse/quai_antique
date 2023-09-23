<?php

namespace App\Controller;

use App\Repository\DateRepository;
use App\Repository\ReservationRepository;
use App\Repository\ScheduleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

function calculateEveningTimestamps($date) {
    $start = date_timestamp_get($date->getEveningStart());
    $end = strtotime('-30 minutes', date_timestamp_get($date->getEveningEnd()));
    
    return [
        'start' => $start,
        'end' => $end
    ];
}
function calculateNoonTimestamps($date) {
    $start = date_timestamp_get($date->getNoonStart());
    $end = strtotime('-30 minutes', date_timestamp_get($date->getNoonEnd()));
    
    return [
        'start' => $start,
        'end' => $end
    ];
}

function getScheduleInfoEvening($search_date, $schedulesRepository) {
    $day = date('N', date_timestamp_get($search_date));
    
    $date = $schedulesRepository->findOneBy(['day' => $day]);
    
    $scheduleInfo = [
        'close' => false,
        'start' => null,
        'end' => null
    ];

    if ($date) {
        if ($date->getEveningClose()) {
            $scheduleInfo['close'] = true;
        } else {
            $timestamps = calculateEveningTimestamps($date);
            $scheduleInfo['start'] = $timestamps['start'];
            $scheduleInfo['end'] = $timestamps['end'];
        }
    }

    return $scheduleInfo;
}
function getScheduleInfoNoon($search_date, $schedulesRepository) {
    $day = date('N', date_timestamp_get($search_date));
    
    $date = $schedulesRepository->findOneBy(['day' => $day]);
    
    $scheduleInfo = [
        'close' => false,
        'start' => null,
        'end' => null
    ];

    if ($date) {
        if ($date->getNoonClose()) {
            $scheduleInfo['close'] = true;
        } else {
            $timestamps = calculateNoonTimestamps($date);
            $scheduleInfo['start'] = $timestamps['start'];
            $scheduleInfo['end'] = $timestamps['end'];
        }
    }

    return $scheduleInfo;
}
function calculatePercentage($reservations, $yamlFilePath) {
    $places = 0;

    foreach($reservations as $reservation) {
        $places += $reservation->getPlaces();
    }

    $datas = Yaml::parseFile($yamlFilePath);
    $max = $datas['places'];
    
    $percentage = ($places * 100) / $max;

    return $percentage;
}


class AdminReservationController extends AbstractController
{
    #[Route('admin/reservations/{service}', name: "admin_reservations", methods: ['GET', 'POST'], defaults: ['service' => 'matin'])]
    public function reservations(string $service, Request $request, ReservationRepository $reservationRepository, 
                DateRepository $dateRepository, ScheduleRepository $schedulesRepository ) 
    {

        $search_date = $request->query->get('search_date');
        if ($search_date) {
            $request->getSession()->set('search_date', $search_date);
        } else if ($request->getSession()->get('search_date')) {
            $search_date = $request->getSession()->get('search_date');
        } else {
            $search_date = new Datetime();
            $search_date = $search_date->format('Y-m-d');
        }
        $search_date = new Datetime($search_date);
        
        $special_date = $dateRepository->findOneBy(['date' => $search_date]);

        $scheduleInfo = [
            'close' => false,
            'start' => null,
            'end' => null
        ];

        if ($service == 'midi') {
            if ($special_date) {
                if ($special_date->getEvening_Close()){
                    $scheduleInfo['close'] = true;
                } else if ($special_date->getEvening_normal()){
                    $scheduleInfo = getScheduleInfoEvening($search_date, $schedulesRepository); 
                } else {
                    $timestamps = calculateEveningTimestamps($special_date);
                    $scheduleInfo['start'] = $timestamps['start'];
                    $scheduleInfo['end'] = $timestamps['end'];
                }
            } else {   
                $scheduleInfo = getScheduleInfoEvening($search_date, $schedulesRepository);
            }
            if (!$scheduleInfo['close']) {
                $reservations = $reservationRepository->adminFindByDate($search_date, 'evening', $scheduleInfo['end']);
                $percentage = calculatePercentage($reservations, $this->getParameter('data'));
            }
        } else {
            if ($special_date) {
                if ($special_date->getNoon_Close()){
                    $scheduleInfo['close'] = true;
                } else if ($special_date->getNoon_normal()){
                    $scheduleInfo = getScheduleInfoNoon($search_date, $schedulesRepository);
                } else {
                    $timestamps = calculateNoonTimestamps($search_date);
                    $scheduleInfo['start'] = $timestamps['start'];
                    $scheduleInfo['end'] = $timestamps['end']; 
                }
            } else {    
                $scheduleInfo = getScheduleInfoNoon($search_date, $schedulesRepository);
            }
            
            if (!$scheduleInfo['close']) {
                $reservations = $reservationRepository->adminFindByDate($search_date, 'noon', $scheduleInfo['start']);
                $percentage = calculatePercentage($reservations, $this->getParameter('data'));
            }
        }
        
        return $this->render('Reservation/admin/reservation.admin.html.twig', [
            'service' => $service,
            'error' => $error ?? null,
            'success' => $success ?? null,
            'close' => $scheduleInfo['close'] ?? null,
            'start' => isset($scheduleInfo['start']) ? date('H:i', $scheduleInfo['start']) : null,
            'end' => isset($scheduleInfo['end']) ? date('H:i', $scheduleInfo['end']) : null,
            'reservations' => $reservations ?? 0,
            'places' => $places ?? 0,
            'percentage' => $percentage ?? 0,
            'search_date' => $search_date->format('Y-m-d'),
        ]);
    }   
}