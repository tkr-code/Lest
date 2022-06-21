<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\DeliverySpaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(DeliverySpaceRepository $deliverySpaceRepository): Response
    {

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    
}
