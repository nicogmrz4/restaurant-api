<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\OrderItem;
use App\Service\OrderService;

class OrderItemProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private OrderService $orderSvc
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?OrderItem
    {
        $pricePerUnit = $data
            ->getFood()
            ->getPrice();

        $data->setPricePerUnit($pricePerUnit);

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $order = $data->getOrder();
        $this->orderSvc->updateTotalItemsAndTotalPriceAndPersist($order);

        return $result;
    }
}
