<?php

namespace App\Service;

use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private PhotoRepository $photoRepository;

    public function __construct(RequestStack $requestStack, PhotoRepository $photoRepository)
    {
        $this->session = $requestStack->getSession();
        $this->photoRepository = $photoRepository;
    }

    public function getSessionCart(): array
    {
        $cart = $this->session->get('cart', []);

        return $cart;
    }

    public function getCart(): array
    {
        $cart = $this->getSessionCart();

        $datas = [];

        $total = 0;
        foreach ($cart as $id => $item) {
            $photo = $this->photoRepository->find($id);
            $quantity = $item['quantity'];
            $datas[] = [
                'photo' => $photo,
                'quantity' => $quantity
            ];

            $total += $photo->getPrice() * $quantity;
        }

        $result = [

            'datas' => $datas,
            'total' => $total
        ];

        return $result;
    }

    public function addToCart(int $id, Request $request): array
    {
        // Je récupère la photo que je souhaite ajouter au panier
        $photo = $this->photoRepository->find($id);
        // if ($photo === null) {
        //     throw $this->createNotFoundException('Photo not found');
        // }

        // Je récupère la quantité de mon formulaire
        $quantity = $request->request->get('quantity');
        if ($quantity === null || $quantity < 1) {
            throw new \InvalidArgumentException('La quantité doit etre un entier strictement positif');
        }

        // Je regarde si la photo est dans le panier
        $cart = $this->getSessionCart();
        if (isset($cart[$id])) {
            // Si quantite est renseignée
            $cart[$id]['quantity'] += $quantity;
        } else {

            // Si quantite est renseignée
            $cart[$id] = [
                'quantity' => $quantity
            ];
        }

        $total = 0;
        // Je calcul la quantité totale des articles du panierr
        foreach ($cart as $item) {
            $total += $item['quantity'];
        }

        return $result = [

            'cart' => $cart,
            'total' => $total
        ];
    }

    public function updateQuantity(int $id, Request $request): array
    {
        // Je récupère la quantité de mon formulaire
        $quantity = $request->request->get('quantity');

        $sessionCart = $this->getSessionCart();

        $sessionCart[$id]['quantity'] = $quantity;
        $this->session->set('cart', $sessionCart);

        $cart = $this->getCart();

        $quantityInCart = 0;
        // Je calcul la quantité totale des articles du panierr
        foreach ($cart['datas'] as $item) {
            $quantityInCart += $item['quantity'];
        }

        return $result = [

            'quantity' => $quantityInCart,
            'datas' => $cart['datas'],
            'total' => $cart['total']
        ];
    }


    public function removeFromCart(int $id): void
    {
        $cart = $this->getSessionCart();

        // Je supprime la photo du panier
        foreach ($cart as $key => $item) {
            if ($key === $id) {
                unset($cart[$key]);
                break;
            }
        }

        // Je sauvegarde le panier mis à jour dans la session
        $this->session->set('cart', $cart);
    }

    public function clearCart(): void
    {
        $this->session->remove('cart');
    }
}
