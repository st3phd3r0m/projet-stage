<?php

namespace App\Repository;

use App\Entity\FrequentlyAskedQuestions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FrequentlyAskedQuestions|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrequentlyAskedQuestions|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrequentlyAskedQuestions[]    findAll()
 * @method FrequentlyAskedQuestions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrequentlyAskedQuestionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrequentlyAskedQuestions::class);
    }

    // /**
    //  * @return FrequentlyAskedQuestions[] Returns an array of FrequentlyAskedQuestions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FrequentlyAskedQuestions
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
