<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categories::class);
    }

    public function searchFilter(array $criteria)
    {
        $query = $this->createQueryBuilder('c')
                ->select('c');

        if( !empty($criteria["search"]) ){
            $query->andWhere('MATCH_AGAINST(c.title, c.meta_tag_title, c.description, c.meta_tag_description) AGAINST (:searchterm boolean) >0')
                ->orWhere("c.keywords LIKE :searchterm2")
                ->orWhere("c.meta_tag_keywords LIKE :searchterm2")
                ->setParameter('searchterm', $criteria["search"])
                ->setParameter('searchterm2', '%'.$criteria["search"].'%');
        }

        $query->getQuery()->getResult();

        return $query;
    }

    // /**
    //  * @return Categories[] Returns an array of Categories objects
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
    public function findOneBySomeField($value): ?Categories
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
