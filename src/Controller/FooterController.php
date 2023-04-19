<?php

namespace App\Controller;

use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class FooterController extends AbstractController
{
    #[Route('/footer-data', name:'footer_data')]
    public function footerData(ScheduleRepository $scheduleRepository): Response
    {
        $schedules = $scheduleRepository->findAll();
        
        $restaurantDatas = Yaml::parseFile($this->getParameter('data'));
        $email = $restaurantDatas['email'];
        $tel = $restaurantDatas['tel'];
        
        $datas = [
            'schedules' => $schedules,
            'email' => $email,
            'tel' => $tel
        ];
                
        return $this->render('partials/_footer.html.twig', [
            'schedules' => $schedules,
            'email' => $email,
            'tel' => $tel
        ]);
    }
}