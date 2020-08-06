<?php

namespace App\Repository;

use App\Entity\Pages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pages[]    findAll()
 * @method Pages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pages::class);
    }

    public function searchFilter(array $criteria)
    {
        $query = $this->createQueryBuilder('p')
                ->select('p')
                ->innerJoin('p.user', 'u');

        if( !empty($criteria["usersFilter"]) ){
            $query->andWhere('u.id = :user')
                ->setParameter('user', $criteria["usersFilter"]);
        }
        if( !empty($criteria["search"]) ){
            $query->andWhere('MATCH_AGAINST(p.title, p.content, p.meta_tag_title, p.meta_tag_description) AGAINST (:searchterm boolean) >0')
                ->orWhere("p.keywords LIKE :searchterm2")
                ->orWhere("p.meta_tag_keywords LIKE :searchterm2")
                ->setParameter('searchterm', $criteria["search"])
                ->setParameter('searchterm2', '%'.$criteria["search"].'%');
        }

        $query->getQuery()->getResult();

        return $query;
    }

    // /**
    //  * @return Pages[] Returns an array of Pages objects
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
    public function findOneBySomeField($value): ?Pages
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
