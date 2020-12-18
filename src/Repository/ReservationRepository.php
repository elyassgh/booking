<?php

namespace App\Repository;

use App\Entity\Reservation;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
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

    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    /*
    * @return Reservation|null
    */
    public function findLastReservation() :?Reservation
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //Sequence generator
    public function generateSequence (): ?string
    {
        if (is_null($this->findLastReservation())) {
            return "0";
        } else {
            $lastRef = $this->findLastReservation()->getReference();
            $referencePieces = explode("A", $lastRef);
            $lastSequence = intval($referencePieces[0]);
            $newSequence = $lastSequence + mt_rand(1,10);
            return strval($newSequence);
        }
    }


    /*
    * @return Reservation|null
    */
    public function findOneByReference($reference): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.reference = :ref')
            ->setParameter('ref', $reference)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /*
    * @return Reservation[]
    */
    public function findAllReservationToday($hotelid)
    {
        $today = new \DateTime('today');
        $today = $today->format('Y-m-d');
        return $this->createQueryBuilder('r')
            ->join('App\Entity\Chambre' , 'c')
            ->andWhere('r.chambre = c.id AND c.hotel = :id')
            ->setParameter('id', $hotelid)
            ->andWhere('r.dateReservation = :today')
            ->setParameter('today',$today)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    * @return Reservation[]
    */
    public function findByInputs($hotelid, $reservationdate, $checkin, $identity)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->join('App\Entity\Chambre' , 'c')
            ->andWhere('r.chambre = c.id AND c.hotel = :id')
            ->setParameter('id', $hotelid)
        ;
        if (!is_null($reservationdate)) {
            $queryBuilder->andWhere('r.dateReservation = :date')
                ->setParameter('date' , $reservationdate);
        }
        if (!is_null($checkin)) {
            $queryBuilder->andWhere('r.checkIn = :checkin')
                ->setParameter('checkin' , $checkin);
        }
        if (!is_null($identity)) {
            $queryBuilder
                ->join('App\Entity\Client' , 'cli')
                ->andWhere("r.client = cli.id AND cli.cinOrPassport = :identity")
                ->setParameter('identity' , $identity);
        }

        return $queryBuilder->getQuery()->getResult();

    }

    //Counting number reservation of a date
    public function numberOfReservationOfDate($date ,$hotelid)
    {
        return intval($this->createQueryBuilder('r')
            ->join('App\Entity\Chambre' , 'c')
            ->andWhere('r.chambre = c.id AND c.hotel = :id')
            ->setParameter('id', $hotelid)
            ->andWhere('r.dateReservation = :date')
            ->setParameter('date', $date)
            ->select('count(r.id)')
            ->getQuery()
            ->getSingleScalarResult());
    }


    //Reservations of each day in the current week
    public function reservationOfTheWeek($hotelid)
    {
        $result = Array();
        $weekstart = (new DateTime('today'))->sub(new DateInterval('P3D'));
        $weekend = (new DateTime('today'))->add(new DateInterval('P4D'));
        $interval = new DateInterval('P1D');
        $week = new DatePeriod($weekstart, $interval, $weekend);

        foreach($week as $weekday) {
            $nbr = $this->numberOfReservationOfDate($weekday,$hotelid);
            $result += [$weekday->format('m-d') => $nbr];
        }

        return $result;
    }

    //Revenue of each day in the current month
    public function partialIncomeOfMonth ($hotelid) {
        $month = (new DateTime('today'))->format('Y-m');
        return $this->createQueryBuilder('r')
            ->join('App\Entity\Chambre' , 'c')
            ->andWhere('r.chambre = c.id AND c.hotel = :id')
            ->andWhere('r.dateReservation LIKE :date')
            ->setParameter('date', $month.'-%')
            ->setParameter('id', $hotelid)
            //IMPORTANT !!! --> COALESCE replaces null with input value in this case 0
            ->groupBy('r.dateReservation')
            ->select('r.dateReservation, COALESCE(SUM(r.total),0) As Revenue')
            ->getQuery()
            ->getScalarResult();
    }


    //Month income REMEMBER !!! --> input format :  YYYY-MM
    public function incomeOfMonth($month, $hotelid) {
        return intval($this->createQueryBuilder('r')
            ->join('App\Entity\Chambre' , 'c')
            ->andWhere('r.chambre = c.id AND c.hotel = :id')
            ->setParameter('id', $hotelid)
            ->andWhere('r.dateReservation LIKE :date')
            ->setParameter('date', $month.'-%')
            //IMPORTANT !!! --> COALESCE replaces null with input value in this case 0
            ->select('COALESCE(SUM(r.total),0)')
            ->getQuery()
            ->getSingleScalarResult());
    }

    //Income of each month in the current Year
    public function yearIncome($hotelid) {

        # $yeartest = $today->format('y'); Avoid using this syntax, it gives bad results !!
        $year = (new DateTime('today'))->format('Y');

        $result = Array();

        for ($i=1; $i<13; $i++) {
            if (strlen($i) === 1 ) $i ='0'.$i;
            $income = $this->incomeOfMonth($year.'-'.$i , $hotelid);
            $result += [ (new DateTime($year.'-'.$i))->format('M') => $income];
        }
        return $result;
    }


}
