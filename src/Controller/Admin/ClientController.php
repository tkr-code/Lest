<?php
namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Personne;
use App\Entity\User;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/admin/client")
 */
class ClientController extends AbstractController
{
    private $repository;
    private $parent_page = 'Client';
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * @Route("/{id}/order", name="admin_client_order")
     */
    public function clientOrder($id, OrderRepository $orderRepository): Response
    {
        return $this->render('admin/client/order/index.html.twig', [
            'nbrOrders'=>count($orderRepository->findAll()),
            'orders'=>$orderRepository->findStateClient($id),
            'ordersCompleted'=>$orderRepository->findStateClient($id ,'completed'),
            'ordersInProgress'=>$orderRepository->findStateClient($id, 'in progress'),
            'ordersWaiting'=>$orderRepository->findStateClient($id, 'waiting'),
            'ordersCanceled'=>$orderRepository->findStateClient($id, 'canceled'),
        ]);
    }
    /**
     * @Route("/", name="admin_client_index")
     */
    public function index():Response
    {
       return $this->render('admin/client/index.html.twig',[
           'clients'=>$this->repository->findAll(),
           'parent_page'=>$this->parent_page
       ]);
    }

    /**
     * @Route("/new", name="admin_client_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $personne = new Personne();
        $personne->setFirstName('Client')->setLastName('Lest');
        $user->setPersonne($personne);
        $user->setEmail('client@mail.com')
        ->setPassword('password')
        ->setPhoneNumber('772495592')
        ->isVerified(true);
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse = $user->getAdresse();
            if($adresse){

                $adresse->setFirstName($user->getPersonne()->getFirstName())
                ->setLastName($user->getPersonne()->getLastName())
                ->setTel($form->get('phone_number')->getData())
                ;
                $user->setAdresse($adresse);
            }
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(['ROLE_CLIENT']);
            $client = new Client();
            $user->setClient($client);
            $user->setIsActive(true);
            // dd($user);

            $em=  $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success','Un nouveau client a été ajouté.');
            return $this->redirectToRoute('admin_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/client/new.html.twig', [
            'form' => $form,
        ]);
    }
    /**
     * @Route("/{id}/edit", name="admin_client_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $client): Response
    {

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success','Le client a été modifié.');
            return $this->redirectToRoute('admin_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/client/edit.html.twig', [
            'form' => $form,
        ]);
    }
}