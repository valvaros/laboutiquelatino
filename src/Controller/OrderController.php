<?php

namespace App\Controller;

use App\Entity\Address;
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
use App\Repository\OrderRepository;

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
    public function index(Cart $cart, Request $request): Response
    {
        if (!($this->getUser()->getAddresses())) {
            return $this->redirectToRoute('account_address_add');
        } else {

            $order = new Order();
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);





        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }


    /**
     * @Route("/commande/recapitulatif", name="order_recap")
     */
    public function add(Cart $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $total = 0;
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $delivery_content = '<br/>' . $delivery->getphone();

            if ($delivery->getCompany()) {
                $delivery_content = '<br/>' . $delivery->getCompany();
            }

            $delivery_content = $delivery->getAddress();
            $delivery_content = $delivery->getPostal() . '' . $delivery->getCity();
            $delivery_content = $delivery->getCountry();

            //Enregistrer ma commande Order()
            // cela persmet d'enregister ma commance avec toute les informations siter en dessous
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt();
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);

            $this->entityManager->persist($order);

            //Enregistrer mes produits OrderDetails()
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $total += $product['product']->getPrice() * $product['quantity'];
                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();



            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'total' => $total,
                'orderId' => $order->getId()
            ]);
        }

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/paiement/{id}", name="paiement")
     */
    public function Paiment(OrderRepository $orderRepository, $id)
    {


        if (!empty($_POST)) :
            $order = $orderRepository->find($id);
            $name = $orderRepository->find($id);
            $expiration= $orderRepository->find($id);


            $name= $_POST['name'];
            $sub = substr($name, - 4);
            $name = 'xxxxxxx'.$sub;
    
            $expiration= $_POST['expiration'];
            $sub = substr($expiration, -4);
            $expiration = 'xxxx' . $sub;


            $number = $_POST['number'];
            $sub = substr($number, -4);
            $number = 'xxxxxxxxxxxx' . $sub;

            return $this->render('order/recapOrder.html.twig', [
                'order' => $order,
                'number' => $number,
                'name'=> $name,
                'expiration'=> $expiration,
            ]);
        endif;

        return $this->render('order/paiement.html.twig', [
            'orderId' => $id

        ]);
    }
}
