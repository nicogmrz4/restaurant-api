<?php

namespace App\Service;

use App\Entity\AnalyticsRecorder\AnalyticsRecorder;
use App\Entity\AnalyticsRecorder\AnalyticsRecorderPerDay;
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
    
        $this->recordToday(1, $record);
    }

    public function recordTotalCustomers() {
        $record = $this->getRecordOrCreateIfItNotExist('total_customers');

        $totalOrders = $record->getValue() + 1;
        $record->setValue($totalOrders);

        $this->em->persist($record);
        $this->em->flush();
    
        $this->recordToday(1, $record);
    }

    public function recordTotalOrdersRevenues(Order $order) {
        $record = $this->getRecordOrCreateIfItNotExist('total_orders_revenues');

        $totalOrdersRevenues = $record->getValue() + $order->getTotalPrice();
        $record->setValue($totalOrdersRevenues);

        $this->em->persist($record);
        $this->em->flush();

        $this->recordToday($order->getTotalPrice(), $record);
    }

    private function recordToday(float $value, AnalyticsRecorder $record) {
        $today = (new \DateTimeImmutable())->setTime(0,0);
        $recordPerDay = $this->analyticsRecPerDayRepo->findOneBy([
            'createdAt' => $today,
            'analyticsRecorder' => $record->getId()
        ]);
        
        if (is_null($recordPerDay)) {
            $recordPerDay = new AnalyticsRecorderPerDay;
            $recordPerDay->setValue($value);
            $recordPerDay->setAnalyticsRecorder($record);
            
            $this->em->persist($recordPerDay);
            $this->em->flush();

            return;
        }
        
        $newValue = $recordPerDay->getValue() + $value;
        $recordPerDay->setValue($newValue);
        $recordPerDay->setAnalyticsRecorder($record);
        
        $this->em->persist($recordPerDay);
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
