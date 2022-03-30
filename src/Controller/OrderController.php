<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart,Request $request): Response
    {
        if ($this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user'=> $this->getUser()
        ]);

        
        return $this->render('order/index.html.twig',[
            'form'=>$form->createView(),
            'cart'=> $cart->getFull()
        ]);
    }


    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart,Request $request): Response
    {
        
        $form = $this->createForm(OrderType::class, null, [
            'user'=> $this->getUser()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
             $date = new DateTime();
             $carriers = $form->get('carriers')->getData();
             $delivery = $form->get('addresses')->getData();
             $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
             $delivery_content = '<br/>'.$delivery->getphone();
             
             if($delivery->getCompany()){
                $delivery_content ='<br/>'. $delivery->getCompany();
             }
            
             $delivery_content = $delivery->getAddress();
             $delivery_content = $delivery->getPostal().''.$delivery->getCity();
             $delivery_content = $delivery->getCountry();
             
            //Enregistrer ma commande Order()
             $order = new Order();
             $order->setUser($this->getUser());
             $order->setCreatedAt($date);
             $order->setCarrierName($carriers->getName());
             $order->setCarrierPrice($carriers->getPrice());
             $order->setDelivery($delivery_content);
             $order->setIsPaid(0);

             $this->entityManager->persist($order);

             //Enregistrer mes produits OrderDetails()
             foreach($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($order);
             }
             
             $this->entityManager->flush();

             Stripe::setApiKey
             
             return $this->render('order/add.html.twig',[
                'cart'=> $cart->getFull(),
                'carrier' => $carriers,
                'delivery'=> $delivery_content
            ]);
        }

        return $this->redirectToRoute('cart');
    }

    
}
