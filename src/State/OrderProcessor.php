<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Order;
use App\Service\OrderService;

class OrderProcessor implements ProcessorInterface
{
    public function __construct(private OrderService $orderService) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Order
    {
        if ($context["operation"] instanceof Patch) {
            $order = $this->orderService->updateQuantitiesAndPersist($data);
            return $order;
        }

        $order = $this->orderService->create($data);

        return $order;
    }
}
