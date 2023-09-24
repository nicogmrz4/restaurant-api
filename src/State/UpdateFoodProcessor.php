<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Food;
use App\Service\FoodService;

class UpdateFoodProcessor implements ProcessorInterface
{
    public function __construct(private FoodService $foodService, private ProcessorInterface $persistProcessor) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Food
    {
        $this->foodService->update($data, $uriVariables['id']);
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        
        return $result;
    }
}
