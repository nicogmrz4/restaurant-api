<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\OrderStatusDTO;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class OrderService
{
    const PEDING_STATUS = 'pending';
    const DELIVERED_STATUS = 'delivered';
    const CANCELLED_STATUS = 'cancelled';
    const AVAILABLE_STATUS = [self::PEDING_STATUS, self::DELIVERED_STATUS, self::CANCELLED_STATUS];

    public function __construct(
        private EntityManagerInterface $em, 
        private ValidatorInterface $validator,
        private AnalyticsRecorderService $analyticsRecSvc) {}

    public function create(Order $order): Order 
    {     
        $this->validateItems($order);
        $order = $this->updateQuantities($order);
        $this->em->persist($order);
        $this->em->flush();
        
        $this->analyticsRecSvc->recordTotalOrders();

        return $order;
    }
    
    public function updateQuantities(Order $order): Order {
        $totalItems = $this->countTotalItems($order);
        $totalPrice = $this->calcTotalPrice($order);
        $order->setTotalItems($totalItems);
        $order->setTotalPrice($totalPrice);
        
        return $order;
    }
    
    public function updateQuantitiesAndPersist(Order $order): Order {
        $this->validateItems($order);
        $totalItems = $this->countTotalItems($order);
        $totalPrice = $this->calcTotalPrice($order);
        $order->setTotalItems($totalItems);
        $order->setTotalPrice($totalPrice);
        
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    private function countTotalItems(Order $order): int
    {
        $itemsCount = 0;
        $items = $order->getItems()
            ->getIterator();

        foreach ($items as $item) {
            $itemsCount += $item->getQuantity();
        }

        return $itemsCount;
    }

    private function calcTotalPrice(Order $order): float 
    {
        $totalPrice = 0.0;
        $items = $order->getItems()
            ->getIterator();

        foreach ($items as $item) {
            $totalPrice += $item->getPricePerUnit() * $item->getQuantity();
        }

        return round($totalPrice, 2);
    }

    public function updateStatus(Order $order, string $status) {
        if (!in_array($status, self::AVAILABLE_STATUS)) {
            throw new UnprocessableEntityHttpException(sprintf('The order status "%s" is unavailable.', $status));
        }

        $order->setStatus($status);

        $this->em->persist($order);
        $this->em->flush();

        if ($status === self::DELIVERED_STATUS) {
            $this->analyticsRecSvc->recordTotalOrdersRevenues($order);
        }

        return new OrderStatusDTO($status);
    }

    private function validateItems(Order $order) {
        $items = $order->getItems()
            ->getIterator();

        $this->validator->validate($items);
    }
}

