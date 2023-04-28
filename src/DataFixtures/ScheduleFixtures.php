<?php

namespace App\DataFixtures;

use App\Entity\Schedule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ScheduleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $days = [
            [
                'evening_start' => null,
                'evening_end' => null,
                'noon_start' => null,
                'noon_end' =>null,
                'evening_close' => true,
                'noon_close' => true,
            ],
            [
                'evening_start' => new \DateTime('1970-01-01 12:00'),
                'evening_end' => new \DateTime('1970-01-01 14:30'),
                'noon_start' => null,
                'noon_end' =>null,
                'evening_close' => false,
                'noon_close' => true,
            ],
            [
                'evening_start' => new \DateTime('1970-01-01 12:00'),
                'evening_end' => new \DateTime('1970-01-01 15:00'),
                'noon_start' => new \DateTime('1970-01-01 19:00'),
                'noon_end' =>new \DateTime('1970-01-01 22:00'),
                'evening_close' => false,
                'noon_close' => false,
            ],
            [
                'evening_start' => new \DateTime('1970-01-01 12:00'),
                'evening_end' => new \DateTime('1970-01-01 14:00'),
                'noon_start' => new \DateTime('1970-01-01 19:00'),
                'noon_end' =>new \DateTime('1970-01-01 22:00'),
                'evening_close' => false,
                'noon_close' => false,
            ],
            [
                'evening_start' => new \DateTime('1970-01-01 12:00'),
                'evening_end' => new \DateTime('1970-01-01 14:30'),
                'noon_start' => new \DateTime('1970-01-01 19:00'),
                'noon_end' =>new \DateTime('1970-01-01 23:00'),
                'evening_close' => false,
                'noon_close' => false,
            ],
            [
                'evening_start' => new \DateTime('1970-01-01 12:00'),
                'evening_end' => new \DateTime('1970-01-01 15:00'),
                'noon_start' => new \DateTime('1970-01-01 19:00'),
                'noon_end' =>new \DateTime('1970-01-01 23:00'),
                'evening_close' => false,
                'noon_close' => false,
            ],
            [
                'evening_start' => new \DateTime('1970-01-01 12:00'),
                'evening_end' => new \DateTime('1970-01-01 15:00'),
                'noon_start' => null,
                'noon_end' => null,
                'evening_close' => false,
                'noon_close' => true,
            ],
        ];

        $i = 1;
        foreach($days as $day) {
            $newDay = new Schedule();
            $newDay->setDay($i)
                ->setEveningStart($day['evening_start'])
                ->setEveningEnd($day['evening_end'])
                ->setNoonStart($day['noon_start'])
                ->setNoonEnd($day['noon_end'])
                ->setEveningClose($day['evening_close'])
                ->setNoonClose($day['noon_close']);

            $manager->persist($newDay);
            $i++;
        }
        
        $manager->flush();
    }
}
