<?php

namespace App\Controller;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PhotoRepository $photoRepository, SessionInterface $session): Response
    {
        $photos = $photoRepository->findAll();

        $cart = $session->get('cart');

        $total = 0;

        // Je regarde si le panier est vide
        if (empty($cart)) {
            return $this->render('home/index.html.twig', [
                'photos' => $photos,
                'cart' => $total,
            ]);
        }

        // Je calcul la quantité totale des articles du panierr
        foreach ($cart as $item) {
            $total += $item['quantity'];
        }

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'cart' => $total,
        ]);
    }
}
