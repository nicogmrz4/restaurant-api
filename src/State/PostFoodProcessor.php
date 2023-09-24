<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Food;
use App\Service\FoodService;

class PostFoodProcessor implements ProcessorInterface
{
    public function __construct(private FoodService $foodService) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->foodService->save($data);
    }
}
