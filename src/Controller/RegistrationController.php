<?php

namespace App\Controller;

use App\Entity\ArticleSearch;
use App\Entity\Client;
use App\Entity\Personne;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\RegistrationClientFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Form\ArticleSearchType;
use App\Service\Email\EmailService;
use App\Service\Service;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\SecurityEvents;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function appRegister(EmailService $emailService, Service $service, Request $request, UserPasswordHasherInterface $passwordEncoder, EventDispatcherInterface $eventDispatcherInterface): Response
    {
        $search = new ArticleSearch();
        $formSearch = $this->createForm(ArticleSearchType::class,$search);
        $user = new User();
        $user->setRoles(['ROLE_CLIENT'])->setCle($service->aleatoire(100));
        
        $personne  =  new Personne();
        $personne->setFirstName('Malick')->setLastName('Tounkara');
        $user->setPersonne($personne);
        

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

           
            $client = new Client();
            $user->setClient($client);
            $user->setIsActive(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@lest.sn', 'Inscription'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('email/confirmation.html.twig')
                    ->context([
                        'user'=>$user,
                        'theme'=>$emailService->theme(1)
                        ])
            );

            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get("security.token_storage")->setToken($token);

            $event =  new SecurityEvents($request);
            $eventDispatcherInterface->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);
            $this->addFlash('success','Votre compte a été enregistré. Vous pouvez poursuivre vos achats');
            // do anything else you need here, like send an email
            return $this->redirectToRoute('articles');
        }

        return $this->render('registration/lest.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('admin');
    }
}