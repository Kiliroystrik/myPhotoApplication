<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'show')]
    public function index(CartService $cartService): Response
    {

        $cart = $cartService->getCart();

        return $this->render('cart/index.html.twig', [
            'datas' => $cart['datas'],
            'total' => $cart['total'],
        ]);
    }

    #[Route('/add/{id}', name: 'add')]
    public function addToCart(Request $request, SessionInterface $session, CartService $cartService, PhotoRepository $photoRepository, int $id): Response
    {

        $result = $cartService->addToCart($id, $request);

        // 🔥 The magic happens here! 🔥
        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            // Je sauvegarde le panier mis à jour dans la session
            $session->set('cart', $result['cart']);
            return $this->render('cart/_quantity.stream.html.twig', ['cart_quantity' => $result['total']]);
        }

        // Redirection vers la page du panier ou autre page spécifiee
        return $this->redirectToRoute('app_home');
    }

    #[Route('/update/{id}', name: 'update')]
    public function updateQuantity(int $id, Request $request, SessionInterface $session, CartService $cartService): Response
    {
        $result = $cartService->updateQuantity($id, $request);

        // 🔥 The magic happens here! 🔥
        if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
            // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

            return $this->render('cart/_costs.stream.html.twig', [
                'cart_quantity' => $result['quantity'],
                'datas' => $result['datas'],
                'total' => $result['total'],
            ]);
        }

        // Redirection vers la page du panier ou autre page spécifiee
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function removeFromCart(int $id, CartService $cartService): Response
    {
        $cartService->removeFromCart($id);

        // Redirection vers la page du panier ou autre page spécifiee
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/clear', name: 'clear')]
    public function clearCart(CartService $cartService): Response
    {

        $cartService->clearCart();
        return $this->redirectToRoute('app_home');
    }
}
