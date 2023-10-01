<?php

namespace App\Repository\AnalyticsRecorder;

use App\Entity\AnalyticsRecorder\AnalyticsRecorderPerDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnalyticsRecorderPerDay>
 *
 * @method AnalyticsRecorderPerDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyticsRecorderPerDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyticsRecorderPerDay[]    findAll()
 * @method AnalyticsRecorderPerDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyticsRecorderPerDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyticsRecorderPerDay::class);
    }

//    /**
//     * @return AnalyticsRecorderPerDay[] Returns an array of AnalyticsRecorderPerDay objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AnalyticsRecorderPerDay
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
