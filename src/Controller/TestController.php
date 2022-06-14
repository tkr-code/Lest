<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(CommentRepository $commentRepository): Response
    {
        
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    
}
