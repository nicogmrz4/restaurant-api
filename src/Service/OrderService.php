<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Validator\ValidatorInterface;

class OrderService
{

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

    private function validateItems(Order $order) {
        $items = $order->getItems()
            ->getIterator();

        $this->validator->validate($items);
    }
}

