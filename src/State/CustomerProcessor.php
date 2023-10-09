<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Customer;
use App\Service\AnalyticsRecorderService;

class CustomerProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor, 
        private ProcessorInterface $removeProcessor,
        private AnalyticsRecorderService $analyticsRecSvc
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Customer
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }
    
        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $this->analyticsRecSvc->recordTotalCustomers();
        return $result;
    }
}
