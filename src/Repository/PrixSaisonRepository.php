<?php

namespace App\Repository;

use App\Entity\PrixSaison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrixSaison|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrixSaison|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrixSaison[]    findAll()
 * @method PrixSaison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrixSaisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrixSaison::class);
    }

    // /**
    //  * @return PrixSaison[] Returns an array of PrixSaison objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PrixSaison
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
