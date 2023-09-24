<?php

namespace App\Repository;

use App\Entity\FoodPriceHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FoodPriceHistory>
 *
 * @method FoodPriceHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoodPriceHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoodPriceHistory[]    findAll()
 * @method FoodPriceHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodPriceHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoodPriceHistory::class);
    }

//    /**
//     * @return FoodPriceHistory[] Returns an array of FoodPriceHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FoodPriceHistory
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
