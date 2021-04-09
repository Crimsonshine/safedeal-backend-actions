<?php

namespace App\Repository;

use App\Entity\ProductList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductList|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductList|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductList[]    findAll()
 * @method ProductList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductList::class);
    }

    // /**
    //  * @return ProductList[] Returns an array of ProductList objects
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
    public function findOneBySomeField($value): ?ProductList
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
