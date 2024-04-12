<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/payment', name: 'app_payment_')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'create', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $session->get('cart');

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
