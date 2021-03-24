<?php

namespace App\Repository;

use App\Entity\Search;
use App\Entity\Product;
use Doctrine\ORM\Query; 
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    private $paginatorInterface;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Product::class);
    }

    

    public function findSearch(Search $search) 
    {
        $query = $this
        ->createQueryBuilder('p')
        ->select('c', 'p')
        ->join('p.category', 'c');

        if(!empty($search->string)){
            $query = $query->andWhere('c.name LIKE :string')
            ->setParameter('string', "%{$search->string}%");
        }

        if(!empty($search->min)){
            $query = $query->andWhere('p.price/100 >= :min')
            ->setParameter('min', $search->min);
        }
        
        if(!empty($search->max)){
            $query = $query->andWhere('p.price/100 <= :max')
            ->setParameter('max', $search->max);
        }

        if(!empty($search->categories)){
            $query = $query
            ->andWhere('c.id IN (:categories)')
            ->setParameter('categories', $search->categories);
        }

         return $query->getQuery()->getResult();


        }
}
