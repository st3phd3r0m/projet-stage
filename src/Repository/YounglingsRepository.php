<?php

namespace App\Repository;

use App\Entity\Younglings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Younglings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Younglings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Younglings[]    findAll()
 * @method Younglings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YounglingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Younglings::class);
    }

    // /**
    //  * @return Younglings[] Returns an array of Younglings objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Younglings
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
