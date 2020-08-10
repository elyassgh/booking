<?php

namespace App\Repository;

use App\Entity\Chambre;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use PhpParser\Node\Expr\Array_;

/**
 * @method Chambre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chambre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chambre[]    findAll()
 * @method Chambre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChambreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambre::class);
    }

    // /**
    //  * @return Chambre[] Returns an array of Chambre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chambre
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function isChambreAvailable($id , $checkin , $checkout): ?bool
    {
       $chambres = $this->createQueryBuilder('c')
           ->leftJoin('App\Entity\Reservation','r' , 'WITH' , 'c.id = r.chambre')
           ->andWhere('c.id = :id')
           ->setParameter('id', $id)
           ->getQuery()
           ->getResult()
       ;

       if (empty($chambres)) return true;

       foreach ($chambres as $chambre) {
           $reservations = $chambre->getReservations();
            foreach ($reservations as $reservation) {
                if ((($reservation->getCheckIn() <= $checkin) && ( $checkin <= $reservation->getCheckOut())) ||
                    (($reservation->getCheckIn() <= $checkout) && ( $checkout <= $reservation->getCheckOut())))
                {
                    return false;
                }
            }
       }

       return true;

    }

     /**
      * @return Chambre[] Returns an array of Chambre objects
      */
    public function findChambresByHotel($hotelId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.hotel = :hotelId')
            ->setParameter('hotelId', $hotelId)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param $rooms
     * @param $stars
     * @param $type
     * @param $distance
     * @param $maxprice
     * @return Chambre[] Returns an array of Chambre objects
     */
    public function filter($rooms, $stars, $type ,$distance, $maxprice)
    {
        $result = array();
        foreach ($rooms as $room) {
            if ($distance==0) {
                if($room->getHotel()->getDistanceCentre() < 1 && $room->getHotel()->getNbrEtoiles()==$stars && $room->getCategorie()==$type) {
                    if($room->getPrixSaison()->getPrix()*$room->getPrixSaison()->getTaux() < $maxprice) {
                        array_push($result, $room);
                    }
                }
            } else {
                if($room->getHotel()->getDistanceCentre() < $distance && $room->getHotel()->getNbrEtoiles()==$stars && $room->getCategorie()==$type) {
                    if($room->getPrixSaison()->getPrix()*$room->getPrixSaison()->getTaux() < $maxprice) {
                        array_push($result, $room);
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @return Chambre[] Returns an array of Chambre objects
     */
    public function findAllChambreByHotel($hotelId)
    {
        return $this->createQueryBuilder('c')
        ->join('App\Entity\Hotel','h')
        ->andWhere('h.id = :hotelId')
        ->andWhere('c.hotel = h.id')
        ->setParameter('hotelId', $hotelId)
        ->getQuery()
        ->getResult()
    ;
    }

    //new query well organized
    /**
     * @return Chambre[] Returns an array of Chambre objects
     */
    public function findByInputs($destination, $checkin, $checkout ,$guests)
    {
            $querybuilder = $this->createQueryBuilder('c')
                ->join('App\Entity\Hotel','h')
                ->andWhere('c.hotel = h.id')
                ->andWhere('h.region = :destination OR h.ville = :destination OR h.nom = :destination ')
                ->setParameter('destination', $destination)
            ;

            if($guests == 0) {
                $query = $querybuilder->getQuery();
            } elseif ($guests <= 3) {
                $query = $querybuilder
                    ->andWhere('c.capacity = :guests')
                    ->setParameter('guests', $guests)
                    ->getQuery();
            }
            else {
                $query = $querybuilder
                    ->andWhere('c.capacity >= :guests')
                    ->setParameter('guests', $guests)
                    ->getQuery();
            }

            $chambres = $query->getResult();

            $disponibleChambres = Array();

            foreach ($chambres as $chambre) {

                $isAvailable = true;

                $reservations = $chambre->getReservations();

                foreach ($reservations as $reservation) {
                    if ( (($reservation->getCheckIn() <= $checkin) && ( $checkin <= $reservation->getCheckOut())) ||
                        (($reservation->getCheckIn() <= $checkout) && ( $checkout <= $reservation->getCheckOut())))
                    {
                        $isAvailable = false;
                        break;
                    }
                }

                if($isAvailable) array_push($disponibleChambres,$chambre);

            }

            return $disponibleChambres;
    }


    //Services list (tags input)
    public function getServicesList($services): string {

        $result = '' ;
        foreach ($services as $service) {
            $result .= $service->getLibelle() . ',';
        }
        return $result = substr($result,0,strlen($result)-1);

    }

    //Most Reserved Room in a month REMEMBER !!! --> input format :  YYYY-MM
    public function mostReservedRoomOfMonth($month, $hotelid)
    {
        return $this->createQueryBuilder('c')
            ->join('App\Entity\Reservation','r')
            ->andWhere('c.hotel = :id')
            ->setParameter('id', $hotelid)
            ->select('c As chambre , COUNT(r.chambre) As reservations ')
            ->andWhere('c.id = r.chambre AND r.dateReservation LIKE :date')
            ->setParameter('date', $month.'-%')
            ->groupBy('r.chambre')
            ->orderBy('reservations','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    //Most Reserved Room for each month in the current year
    public function mostReservedRooms($hotelid)
    {
        $result = Array();
        $year = (new DateTime('today'))->format('Y');

        for ($i=1; $i<13; $i++) {
            if (strlen($i) === 1 ) $i ='0'.$i;
            $mostReservedRoom = $this->mostReservedRoomOfMonth($year.'-'.$i, $hotelid);
            if (is_null($mostReservedRoom)) {
                $result += [ (new DateTime($year.'-'.$i))->format('M') => 'N/A'];
                continue;
            }
            $result += [ (new DateTime($year.'-'.$i))->format('M') => $mostReservedRoom['chambre']->getNumero()];
        }
        return $result;
    }

}