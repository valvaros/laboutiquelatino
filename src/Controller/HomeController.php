<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SessionInterface $session )
    {
     
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */

    public function contact()
    {
        return $this->render('home/contact.html.twig');
    }
}
