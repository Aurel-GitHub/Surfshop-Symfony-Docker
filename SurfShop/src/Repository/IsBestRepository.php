<?php

namespace App\Repository;

use App\Entity\IsBest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IsBest|null find($id, $lockMode = null, $lockVersion = null)
 * @method IsBest|null findOneBy(array $criteria, array $orderBy = null)
 * @method IsBest[]    findAll()
 * @method IsBest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IsBestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IsBest::class);
    }

    // /**
    //  * @return IsBest[] Returns an array of IsBest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IsBest
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
