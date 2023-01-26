<?php

namespace App\Repository;

use App\Entity\Schedules\Schedules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FindScheduleFromDate extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedules::class);
    }

    public function findSchedulesDependingDate($date, $service)
    {

    }
}

