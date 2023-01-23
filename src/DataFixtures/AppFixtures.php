<?php

namespace App\DataFixtures;

use App\Entity\Bookings;
use App\Entity\Scedules;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 0; $i < 7; $i++) {
            $day = new Scedules();
            $day->setDay($i);
            if ($i === 5) {
                $day->setNoonStart(null);
    
                $day->setNoonEnd(null);

                $eveningStart = new \DateTime();
                $eveningStart->setTime(19, 00);
                $day->setEveningStart($eveningStart);
    
                $eveningEnd = new \DateTime();
                $eveningEnd->setTime(21, 00);
                $day->setEveningEnd($eveningEnd);

            } else if ($i === 6) {
                $noonStart = new \DateTime();
                $noonStart->setTime(12, 00);
                $day->setNoonStart($noonStart);
    
                $noonEnd = new \DateTime();
                $noonEnd->setTime(15, 30);
                $day->setNoonEnd($noonEnd);
    
                $day->setEveningStart(null);
    
                $day->setEveningEnd(null);

            } else {
                $noonStart = new \DateTime();
                $noonStart->setTime(12, 00);
                $day->setNoonStart($noonStart);
    
                $noonEnd = new \DateTime();
                $noonEnd->setTime(15, 30);
                $day->setNoonEnd($noonEnd);
    
                $eveningStart = new \DateTime();
                $eveningStart->setTime(19, 00);
                $day->setEveningStart($eveningStart);
    
                $eveningEnd = new \DateTime();
                $eveningEnd->setTime(21, 00);
                $day->setEveningEnd($eveningEnd);
            }

            $day->setPlaces(30);
            $manager->persist($day);
        }

        $booking = new Bookings();
        $booking->setName('Erika');
        $booking->setPlaces(2);
        $booking->setAllergies(['céréales(gluten)', 'lait']);
        $booking->setAllergiesOther('Les cons');

        $date = new \DateTimeImmutable('2023-10-03');
        $booking->setDate($date);
        $booking->setService(0);

        $horora = new \DateTime('12:30');
        $booking->setScedule($horora);
        
        $manager->persist($booking);

        $manager->flush();
    }
}
