<?php

namespace App\Dto;

class OrderStatusDTO
{
    private ?string $status = null;

    public function __construct(string $status) {
        $this->status = $status;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
