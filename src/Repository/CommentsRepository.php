<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comments::class);
    }

    public function searchFilter(array $criteria)
    {
        $query = $this->createQueryBuilder('c')
                ->select('c');

        if( !empty($criteria["isModaratedFilter"]) ){
            $query->andWhere('c.isModerated = :isModerated')
                ->setParameter('isModerated', ($criteria["isModaratedFilter"]==="true")?true:false );
        }
        if( !empty($criteria["search"]) ){
            $query->andWhere('MATCH_AGAINST(c.pseudo, c.content) AGAINST (:searchterm boolean) >0')
                ->setParameter('searchterm', $criteria["search"]);
        }

        $query->getQuery()->getResult();

        return $query;
    }

    // /**
    //  * @return Comments[] Returns an array of Comments objects
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
    public function findOneBySomeField($value): ?Comments
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
