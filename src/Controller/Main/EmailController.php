<?php

namespace App\Controller\Main;

use App\Service\Email\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class EmailController extends AbstractController
{
    private $emailService;
    private $mailer;
    public function __construct(EmailService $emailService, MailerInterface $mailerInterface )
    {
        $this->emailService = $emailService;
        $this->mailer = $mailerInterface;
    }
    /**
     * @Route("gestion-compte/edit-email", name="edit_email")
     */
    public function editEmail(): Response
    {
        return $this->render('main/edit-email.html.twig', [
        ]);
    }
    /**
     * @Route("/send/edit-email", name="send_edit_email", methods={"GET","POST"} )
     */
    public function editEmailResponse(Request $request):Response
    {
        $user = $this->getUser();
        $email = (new TemplatedEmail())
            ->from(new Address('malick.tounkara.1@gmail.com', 'app.tkr'))
            ->to($user->getEmail())
            ->subject("Modiifer l'email")
            ->htmlTemplate('email/reset-email.html.twig')
            ->context([
                'user'=>$this->getUser(),
                'theme'=> $this->emailService->theme(8),
            ]);
        try
        {
            $this->mailer->send($email);
            return new JsonResponse('success',200);
        } catch (TransportExceptionInterface $e) 
        {
            if($e->getMessage()){
                return new JsonResponse('error',200);
            }   
        }

    }
    /**
     * @Route("gestion-compte/delete-account/{token}/{id}", name="delete_account")
     */
    public function deleteAccount($token, $id): Response
    {
        if (!$this->isCsrfTokenValid('delete_account', $token)) {
            throw new AccessDeniedException('Acces denied');
        }
        
        return $this->render('main/delete-account.html.twig', [
            'id'=>$id
        ]);
    }
    
    /**
     * @Route("/send/delete-account", name="send_delete_account", methods={"GET","POST"} )
     */
    public function deleteAccountResponse(Request $request):Response
    {
        $user = $this->getUser();
        $email = (new TemplatedEmail())
            ->from(new Address('malick.tounkara.1@gmail.com', 'app.tkr'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('email/delete-account.html.twig')
            ->context([
                'user'=>$user,
                'theme'=> $this->emailService->theme(7),
            ]);
        try
        {
            $this->mailer->send($email);
            return new JsonResponse('success',200);
        } catch (TransportExceptionInterface $e) 
        {
            if($e->getMessage()){
                return new JsonResponse('error',$e->getCode());
            }   
        }

    }
}
