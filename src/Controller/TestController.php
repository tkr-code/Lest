<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\DeliverySpaceRepository;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test/{id}", name="test")
     */
    public function index(Order $order, OrderService $orderService): Response
    {
        // $orderService->orderSendToEmail($order);
        return $this->render('pdf/order.html.twig',['order'=>$order]);
    }
    
}
