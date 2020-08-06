<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    public function findMaxRef()
    {
        return $this->createQueryBuilder('p')
            ->select('MAX(p.reference)')
            ->getQuery()
            ->getResult();
    }

    public function searchFilter(array $criteria)
    {
        $query = $this->createQueryBuilder('p')
                ->select('p')
                ->innerJoin('p.category', 'c');

        if( !empty($criteria["categoryFilter"]) ){
            $query->andWhere('c.id = :category')
                ->setParameter('category', $criteria["categoryFilter"]);
        }
        if( !empty($criteria["search"]) ){
            $query->andWhere('MATCH_AGAINST(p.title, p.meta_tag_title, p.description, p.meta_tag_description) AGAINST (:searchterm boolean) >0')
                ->orWhere("p.keywords LIKE :searchterm2")
                ->orWhere("p.meta_tag_keywords LIKE :searchterm2")
                ->setParameter('searchterm', $criteria["search"])
                ->setParameter('searchterm2', '%'.$criteria["search"].'%');
        }

        $query->getQuery()->getResult();

        return $query;
    }

    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
