<?php
namespace App\Service\Order;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Payment;
use App\Entity\PaymentMethod;
use App\Entity\Shipping;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Service\Email\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Address;


class OrderService{
    private $em;
    private $repository;
    private $orderRepository;
    private $emailService;
    public function __construct(EmailService $emailService, EntityManagerInterface $manager, OrderItemRepository $repository, OrderRepository $orderRepository)
    {
        $this->em = $manager;
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->emailService = $emailService;
    }

    public function orderSendToEmail(Order $order){
        return (new TemplatedEmail())
            ->from(new Address('contact@lest.sn', 'lest.sn'))
            ->to($order->getUser()->getEmail())
            ->subject('Lest - Avis de facture')
            ->htmlTemplate('email/order.html.twig')
            // ->attachFromPath($this->getParameter('order_pdf_directory').DIRECTORY_SEPARATOR.$order->getFacture().'.pdf',$order->getFacture(),'application/pdf')
            ->attach('/public/pdf/order/'.$order->getFacture().'.pdf',$order->getFacture(),'application/pdf')
            // ->attachFromPath()
            ->context([
                // 'user'=>$user,
                'theme' => $this->emailService->theme(4),
                'order' => $order,
            ]);
    }
    public function voiceNumber(int $id)
    {
        return   sprintf("%06s", $id);
    }
    public function addQuantity(int $id, $qty)
    {
        $orderItem = $this->repository->find($id);
        $orderItem->setQuantity($qty);
        $this->em->flush();
        return $orderItem->getCommande()->getId();
        
    }
    public function update(int $id,OrderItem $orderItem)
    {
        $update = $this->repository->find($id);
        $orderItem->setArticle($update->getArticle());
    }

    /**
     * Ajouter la commade au ligne de commande
     * calcul le total de chaque ligne
     * et le total de la commande
     *
     * @param OrderItem $orderItem
     * @param Order $order
     * @return void
     */
    public function orderItemAdd(OrderItem $orderItem, Order $order)
    {
        $orderItem->setCommande($order);
        $this->priceItem($orderItem);
        $orderItem->setUnitsTotal($this->subTotatlItem($orderItem));
        $orderItem->setTotal($orderItem->getUnitsTotal());
        
    }

    /**
     * Recuper le nom et le prix d'une ligne de la commande
     *
     * @param [type] $orderItem
     * @return int
     */
    public function priceItem($orderItem)
    {
        $article = $orderItem->getArticle();
        $orderItem->setProduitName($article->getTitle());
        $price = ($orderItem->getUnitPrice() == 0 )? $article->getPrice(): $orderItem->getUnitPrice();
        $orderItem->setUnitPrice($price);

    }

    /**
     * calcul le total d'une ligne de la commande/facture
     *
     * @param OrderItem $orderItem
     * @return int
     */
    public function subTotatlItem(OrderItem $orderItem)
    {
       return $orderItem->getUnitPrice() * $orderItem->getQuantity();
    }

    /**
     * Calcul le sous total
     *
     * @param Order $order
     * @return int
     */
    public function subTotal(Order $order)
    {
        $subTotalItem = 0;
        // on fait la somme de prix unitaire par la quantite
        foreach($order->getOrderItem() as $item)
        {
            $subTotalItem += $this->subTotatlItem($item);
        }
        return $subTotalItem;
    }
        
    /**
     * Calcul le total d'une commande 
     * total :
     *
     * @param  mixed $order
     * @return order
     */
    public function total(Order $order)
    {
       $order->setTotal($this->subTotal($order) + $order->getAdjustmentsTotal());

        return $order;
    }

    /**
     * calcul avant un insertion en bd
     *
     * @param Order $order
     * @return Order
     */
    public function calculPersist(Order $order){
        $payment = $order->getPayment();
        $payment->setAmount(0);
        $order->setPaymentDue(new \DateTime('+ 5 day') );
        $order->setItemsTotal(0);
        $order->setState('in progress');
        $order->setAdjustmentsTotal(0);
        $numer = empty($order->getId()) ? 1:$order->getId();
        $order->setNumber($this->voiceNumber($numer));
        $order->setTotal(0);
        return $order;
    }

    /**
     * Calcul la commande
     *
     * @param Order $order
     * @return void
     */
    public function calculOrder(Order $order)
    {
        $order->setItemsTotal($this->subTotal($order));
        // $street = $order->getStreet();
        // if($street){
        //     $order->setAdjustmentsTotal($street()->getShippingAmount()->getAmount());
        // }
        $order = $this->total($order);
        $this->em->flush($order);
    }
}