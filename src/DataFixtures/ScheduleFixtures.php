<?php

namespace App\DataFixtures;

use App\Entity\Schedule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ScheduleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $i = 1;
        while($i <= 7) {
            $dataDay = new Schedule();
            $dataDay->setDay($i);
            $manager->persist($dataDay);
            $i++;
        }
        
        $manager->flush();
    }
}
