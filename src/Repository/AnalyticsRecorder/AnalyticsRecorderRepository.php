<?php

namespace App\Repository\AnalyticsRecorder;

use App\Entity\AnalyticsRecorder\AnalyticsRecorder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnalyticsRecorder>
 *
 * @method AnalyticsRecorder|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyticsRecorder|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyticsRecorder[]    findAll()
 * @method AnalyticsRecorder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyticsRecorderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyticsRecorder::class);
    }

//    /**
//     * @return AnalyticsRecorder[] Returns an array of AnalyticsRecorder objects
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

//    public function findOneBySomeField($value): ?AnalyticsRecorder
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
