<?php

namespace App\Service;

use App\Entity\AnalyticsRecorder\AnalyticsRecorder;
use App\Entity\Order;
use App\Repository\AnalyticsRecorder\AnalyticsRecorderPerDayRepository;
use App\Repository\AnalyticsRecorder\AnalyticsRecorderRepository;
use Doctrine\ORM\EntityManagerInterface;

class AnalyticsRecorderService
{
    public function __construct(
        private AnalyticsRecorderRepository $analyticsRecRepo,
        private AnalyticsRecorderPerDayRepository $analyticsRecPerDayRepo,
        private EntityManagerInterface $em
     ) {}

    public function recordTotalOrders() {
        $record = $this->getRecordOrCreateIfItNotExist('total_orders');

        $totalOrders = $record->getValue() + 1;
        $record->setValue($totalOrders);

        $this->em->persist($record);
        $this->em->flush();
    }

    public function recordTotalOrdersRevenues(Order $order) {
        $record = $this->getRecordOrCreateIfItNotExist('total_orders_revenues');

        $totalOrdersRevenues = $record->getValue() + $order->getTotalPrice();
        $record->setValue($totalOrdersRevenues);

        $this->em->persist($record);
        $this->em->flush();
    }

    private function getRecordOrCreateIfItNotExist(string $recordName) {
        $record = $this->analyticsRecRepo->findOneBy(['record' => $recordName]);

        if (is_null($record)) {
            $newRecord = new AnalyticsRecorder;
            $newRecord->setRecord($recordName);

            $this->em->persist($newRecord);
            $this->em->flush();

            return $newRecord;
        }

        return $record;
    }
}
