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

class AdminReservationController extends AbstractController
{
    #[Route('admin/reservations/{service}', name: "admin_reservations", methods: ['GET', 'POST'], defaults: ['service' => 'matin'])]
    public function reservations(string $service, Request $request, ReservationRepository $reservationRepository, 
                DateRepository $dateRepository, ScheduleRepository $schedulesRepository ) 
    {

        $search_date = $request->query->get('search_date');
        if ($search_date) {
            $request->getSession()->set('search_date', $search_date);
            $search_date = new Datetime($search_date);
        } else if ($request->getSession()->get('search_date')) {
            $search_date = $request->getSession()->get('search_date');
            $search_date = new Datetime($search_date);
        } else {
            $search_date = new Datetime();
            $search_date = $search_date->format('Y-m-d');
            $search_date = new Datetime($search_date);
        }

        $special_date = $dateRepository->findOneBy(['date' => $search_date]);
        
        if ($service == 'midi') {
            if ($special_date) {
                if ($special_date->getEvening_Close()){
                    $close = true;
                } else if ($special_date->getEvening_normal()){
                    $day = date('N', date_timestamp_get($search_date));
                    
                    $date = $schedulesRepository->findOneBy(['day'=> $day]);
                    if ($date->getEveningClose()) {
                        $close = true;
                    } else {
                        $schedules = [];
                        $start = date_timestamp_get($date->getEveningStart());
                        $end = strtotime('-30 minutes', date_timestamp_get($date->getEveningEnd()));

                        $time = $start;
                        while($time <= $end) {
                            array_push($schedules, $time);
                            $time = strtotime('+15 minutes', $time);
                        }
                    }
                } else {
                    $schedules = [];
                    $start = date_timestamp_get($special_date->getEveningStart());
                    $end = strtotime('-30 minutes', date_timestamp_get($special_date->getEveningEnd()));

                    $time = $start;
                    while($time <= $end) {
                        array_push($schedules, $time);
                        $time = strtotime('+15 minutes', $time);
                    }
                }
            } else {    
                $day = date('N', date_timestamp_get($search_date));
                $date = $schedulesRepository->findOneBy(['day' => $day]);
                if ($date->getEveningClose()) {
                    $close = true;
                } else {
                    $schedules = [];
                    $start = date_timestamp_get($date->getEveningStart());
                    $end = strtotime('-30 minutes', date_timestamp_get($date->getEveningEnd()));

                    $time = $start;
                    while($time <= $end) {
                        array_push($schedules, $time);
                        $time = strtotime('+15 minutes', $time);
                    }
                }
            }
            if (!isset($close)) {
                $reservations = $reservationRepository->adminFindByDate($search_date, 'evening', $end);
                
                $places = 0;
                foreach($reservations as $reservation) {
                    $places += $reservation->getPlaces();
                }

                $datas = Yaml::parseFile($this->getParameter('data'));
                $max = $datas['places'];
                $percentage = ($places * 100)/$max;
            }
        } else {
            if ($special_date) {
                if ($special_date->getNoon_Close()){
                    $close = true;
                } else if ($special_date->getNoon_normal()){
                    $day = date('N', date_timestamp_get($search_date));
                    
                    $date = $schedulesRepository->findOneBy(['day'=> $day]);
                    if ($date->getNoonClose()) {
                        $close = true;
                    } else {
                        $schedules = [];
                        $start = date_timestamp_get($date->getNoonStart());
                        $end = strtotime('-30 minutes', date_timestamp_get($date->getNoonEnd()));
                    }
                } else {
                    $schedules = [];
                    $start = date_timestamp_get($special_date->getNoonStart());
                    $end = strtotime('-30 minutes', date_timestamp_get($special_date->getNoonEnd()));
                }
            } else {    
                $day = date('N', date_timestamp_get($search_date));
                $date = $schedulesRepository->findOneBy(['day' => $day]);
                if ($date->getNoonClose()) {
                    $close = true;
                } else {
                    $schedules = [];
                    $start = date_timestamp_get($date->getNoonStart());
                    $end = strtotime('-30 minutes', date_timestamp_get($date->getNoonEnd()));
                }
            }
            
            if (!isset($close)) {
                $reservations = $reservationRepository->adminFindByDate($search_date, 'noon', $start);
                $places = 0;
                foreach($reservations as $reservation) {
                    $places += $reservation->getPlaces();
                }
                $datas = Yaml::parseFile($this->getParameter('data'));
                $max = $datas['places'];
                $percentage = ($places * 100)/$max;
            }
        }
        
        return $this->render('Reservation/admin/reservation.admin.html.twig', [
            'service' => $service,
            'error' => $error ?? null,
            'success' => $success ?? null,
            'close' => $close ?? null,
            'start' => isset($start) ? date('H:i', $start) : null,
            'end' => isset($end) ? date('H:i', $end) : null,
            'reservations' => $reservations ?? 0,
            'places' => $places ?? 0,
            'percentage' => $percentage ?? 0,
            'search_date' => $search_date->format('Y-m-d'),
        ]);
    }   
}