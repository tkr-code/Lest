<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\DeliverySpaceRepository;
use App\Service\Order\OrderService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(MailerInterface $mailerInterface): Response
    {
        // $email = (new TemplatedEmail())
        // ->from((new Address('contact@lest.sn','Lest')))
        // ->to('malick.tounkara.1@gmail.com')
        // ->addTo('contact@lest.sn')
        // ->subject('Lest test multiemail')
        // ->text('Malic test')
        // ;
        // $mailerInterface->send($email);
        return $this->render('test/index.html.twig');
    }
    
}
