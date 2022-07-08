<?php

namespace App\Controller;
// le controllers permet de réceptionner une requête(triée par une route )et de définir la réponse appropriée(exemple une route)
// Afin de creer nimporte quel chose sur symfony on utilise make: par exemplou pour creer une entiter nous utilisons php bin/concole make:entity

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

     /**
     * @Route("/compte/ajouter-une-adresses", name="account_address_add")
     */
    public function add(Cart $cart, Request $request)
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            if ($cart->get()){
                return $this->redirectToRoute('order');
            }
               
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_edit")
     */

    public function edit(Request $request, $id)
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneBy(['id'=>$id]);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_add.html.twig', [
            'form'=>$form->createView()
        ]);


        
    }


    /**
     * @Route("/compte/supprimer-une-adresse/{id}", name="account_address_delete")
     */

    public function delete($id)
    {
        $address = $this->entityManager->getRepository->find($id);

        if(!$address || $address->getUser() != $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
            
        }
        
        $this->entityManager->flush();
        return $this->redirectToRoute('account_address');
    }
}
