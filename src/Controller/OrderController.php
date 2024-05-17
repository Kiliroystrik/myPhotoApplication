<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\PhotoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(OrderRepository $orderRepository, UserInterface $user, UserRepository $userRepository): Response
    {
        // Je récupère le customer 
        $userIdentified = $user->getUserIdentifier();
        $userIdentified = $userRepository->findOneBy(['email' => $userIdentified]);
        $customer = $userIdentified->getCustomer();

        $orders = $orderRepository->findBy(['customer' => $customer->getId()]);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, UserInterface $user, UserRepository $userRepository, PhotoRepository $photoRepository, OrderRepository $orderRepository): Response
    {
        $order = new Order();

        // Je récupère le customer 
        $userIdentified = $user->getUserIdentifier();
        $userIdentified = $userRepository->findOneBy(['email' => $userIdentified]);
        $customer = $userIdentified->getCustomer();

        $order->setCustomer($customer);
        // Je récupère mon panier

        $cart = $session->get('cart');

        // Pour chaque item du panier je créer un nouveau OrderItem
        foreach ($cart as $item => $value) {

            $orderItem = new OrderItem();
            $orderItem->setQuantity($value['quantity']);

            // Je cherche la photo correspondante
            $photo = $photoRepository->findOneBy(['id' => $item]);
            $orderItem->setPrice($photo->getPrice());
            $orderItem->setPhoto($photo);
            $orderItem->setOrder($order);
            $order->addOrderItem($orderItem);
        }

        // Je persist mon order

        $orderRepository->save($order, true);

        $session->remove('cart');


        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }

    // route pour la validation et complétion d'une commande

    #[Route('/complete/{order}', name: 'app_order_complete', methods: ['GET'])]
    public function complete(): Response
    {
        return $this->render('order/complete.html.twig', []);
    }


    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order, UserInterface $user, UserRepository $userRepository): Response
    {
        // Je récupère le customer 
        $userIdentified = $user->getUserIdentifier();
        $userIdentified = $userRepository->findOneBy(['email' => $userIdentified]);
        $customer = $userIdentified->getCustomer();

        // Je calcul le prix total
        $totalPrice = 0;
        foreach ($order->getOrderItems() as $orderItem) {
            $totalPrice += $orderItem->getPrice() * $orderItem->getQuantity();
        }

        // Si mon customer ne possède pas cet order, je renvoie une 403
        if (!$order->getCustomer()->getId() == $customer->getId()) {
            // TO-DO : renvoyer une page d'erreur
            return $this->redirectToRoute('app_home');
        } else {
            return $this->render('order/show.html.twig', [
                'order' => $order,
                'totalPrice' => $totalPrice
            ]);
        }
    }
}
