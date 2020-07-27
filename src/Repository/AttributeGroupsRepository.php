<?php

namespace App\Repository;

use App\Entity\AttributeGroups;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AttributeGroups|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttributeGroups|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttributeGroups[]    findAll()
 * @method AttributeGroups[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttributeGroupsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttributeGroups::class);
    }

    // /**
    //  * @return AttributeGroups[] Returns an array of AttributeGroups objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AttributeGroups
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
