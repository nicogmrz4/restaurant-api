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
        $order = $this->orderService->create($data);

        return $order;
    }
}
