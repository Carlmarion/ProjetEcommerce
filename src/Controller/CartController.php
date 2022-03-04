<?php

namespace App\Controller;

use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(CartService $cs): Response
    {
        $cartWithData = $cs->getCartWithData();
        $total = $cs->getTotal();

        return $this->render('cart/index.html.twig', [
            'produits' => $cartWithData,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/add/{id}/{route}", name="cart_add")
     */
    public function add($id, $route, CartService $cs)
    {
        $cs->add($id);

        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/cart/drop/{id}/{route}", name="cart_drop")
     */
    public function drop($id, $route, CartService $cs)
    {
        $cs->drop($id);

        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cs)
    {
        $cs->remove($id);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/dropall", name="cart_dropall")
     */
    public function dropAll(CartService $cs)
    {
        $user = $this->getUser();

        $cs->dropAll();

        return $this->render('/cart/checkout.html.twig', [
            'user' => $user
        ]);
    }


    

    


}
