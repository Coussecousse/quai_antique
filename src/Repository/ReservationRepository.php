<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\PseudoTypes\False_;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByDate(DateTime $date, User $client) {
        $reservationsOfTheClient = $this->createQueryBuilder('r')
                            ->andWhere('r.client = :client')
                            ->setParameter('client', $client)
                            ->getQuery()
                            ->getResult();

        $dates = [];
        foreach($reservationsOfTheClient as $reservation) {
            array_push($dates, $reservation->getDate());
        }
        foreach($dates as $dateReservation) {
            $dateReservation = new Datetime(date('Y-m-d',  date_timestamp_get($dateReservation)));
            dump($dateReservation);
            dump($date);
            if ($dateReservation == $date)
            {
                return true;
            }
        }
        return false;
    }
    public function findAfterDateReservation(Datetime $date) {
        return $this->createQueryBuilder('r')
                ->where('r.date >= :date')
                ->setParameter('date', $date)
                ->getQuery()
                ->getResult();
    }
    public function adminFindByDate(DateTime $date, string $service, int $hourToCompare) {
        $allReservations = $this->findAll();
        $reservations = [];
        foreach($allReservations as $reservation) {
            $date_reservation = $reservation->getDate();
            $day = $date_reservation->format('Y-m-d');
            $hour = $date_reservation->format('H:i');
            $hour = date_timestamp_get(new DateTime('1970-01-01 '.$hour));
            $day = new DateTime($day);
            if ($day == $date) {
                if ($service == 'evening') {
                    if ($hour <= $hourToCompare) {
                        array_push($reservations, $reservation);
                    }
                } else {
                    if ($hour >= $hourToCompare) {
                        array_push($reservations, $reservation);
                    }   
                }
            }
        }
        return $reservations;
    }
//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
