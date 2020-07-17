<?php

namespace App\Repository;

use App\Entity\Chambre;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
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

    /**
     * @return Chambre[] Returns an array of Chambre objects
     */
    public function findChambresDisponibleByHotelForOneDay($hotelId)
    {
        $checkin = new DateTime('today');
        $checkin = $checkin->format('Y-m-d H:i:s');

        $checkout = new DateTime('tomorrow');
        $checkout = $checkout->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('c')
            ->leftJoin('App\Entity\Reservation','r' , 'WITH' , 'c.id = r.chambre')
            ->join('App\Entity\Hotel','h')
            ->andWhere('h.id = :hotelId')
            ->andWhere('c.hotel = h.id')
            ->andWhere('r.checkIn IS NULL OR :checkin NOT BETWEEN r.checkIn AND r.checkOut')
            ->andWhere('r.checkOut IS NULL OR :checkout NOT BETWEEN r.checkIn AND r.checkOut')
            ->setParameter('hotelId', $hotelId)
            ->setParameter('checkin', $checkin)
            ->setParameter('checkout', $checkout)
            ->getQuery()
            ->getResult()
            ;
    }


     /**
      * @return Chambre[] Returns an array of Chambre objects
      */
    public function findByInputs($destination, $checkin, $checkout ,$guests)
    {
        if($guests<4) {
            return $this->createQueryBuilder('c')
                ->leftJoin('App\Entity\Reservation','r' , 'WITH' , 'c.id = r.chambre')
                ->join('App\Entity\Hotel','h')
                ->andWhere('c.hotel = h.id')
                ->andWhere('c.capacity = :guests')
                ->andWhere('(r.checkIn IS NULL) OR (:checkin NOT BETWEEN r.checkIn AND r.checkOut)')
                ->andWhere('(r.checkOut IS NULL) OR (:checkout NOT BETWEEN r.checkIn AND r.checkOut) ')
                ->andWhere('h.region = :destination OR h.ville = :destination OR h.nom = :destination ')
                ->setParameter('checkin', $checkin)
                ->setParameter('checkout', $checkout)
                ->setParameter('destination', $destination)
                ->setParameter('guests', $guests)
                ->getQuery()
                ->getResult()
                ;
        } else {
            return $this->createQueryBuilder('c')
                ->leftJoin('App\Entity\Reservation','r' , 'WITH' , 'c.id = r.chambre')
                ->join('App\Entity\Hotel','h')
                ->andWhere('c.hotel = h.id')
                ->andWhere('c.capacity >= :guests')
                ->andWhere('(r.checkIn IS NULL) OR (:checkin NOT BETWEEN r.checkIn AND r.checkOut)')
                ->andWhere('(r.checkOut IS NULL) OR (:checkout NOT BETWEEN r.checkIn AND r.checkOut)')
                ->andWhere('h.region = :destination OR h.ville = :destination OR h.nom = :destination')
                ->setParameter('checkin', $checkin)
                ->setParameter('checkout', $checkout)
                ->setParameter('destination', $destination)
                ->setParameter('guests', $guests)
                ->getQuery()
                ->getResult()
                ;
        }
    }

}