<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class OrderStatusController extends AbstractController
{  
    public function __construct(private OrderService $orderSvc) {}

    public function __invoke(Order $order, Request $request)
    {
        $status = $request->toArray()['status'];

        $response = $this->orderSvc->updateStatus($order, $status);

        return $response;
    }
}
