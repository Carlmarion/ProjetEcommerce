<?php 

namespace App\Service\Cart;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $rs;
    private $repo;
    private $image;

    public function __construct(RequestStack $rs, ProduitRepository $repo)
    {
        $this->rs = $rs;
        $this->repo = $repo;
        
    }

    public function add($id)
    {
        $session = $this->rs->getSession();

        $cart = $session->get('panier', []);


        if(!empty($cart[$id]))
        {
            $cart[$id]++;
        }
        else
        {
            $cart[$id] = 1;
        }
        $session->set('panier', $cart);
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('panier', []);

        if(!empty($cart[$id]))
        {
            unset($cart[$id]);
        }
        $session->set('panier',$cart);
    }

    public function drop($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('panier', []);

        if($cart[$id] > 1)
        {
            $cart[$id]--;
        }
        else
        {
            unset($cart[$id]);
        }
        $session->set('panier', $cart);
    }

    public function dropAll()
    {
        $session = $this->rs->getSession();

        $session->set('panier', []);
    }

    public function getCartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('panier', []);

        $cartWithData = [];

        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'produit' => $this->repo->find($id),
                'quantity' => $quantity
            ];
        }
        return $cartWithData;
    }

    
    public function getTotal()
    {
        $total = 0;
        $cartWithData = $this->getCartWithData();

        foreach($cartWithData as $item)
        {
            $totalItem = $item['produit']->getPrix() * $item['quantity'];
            $total += $totalItem;
        }
        return $total;
        //dd($cartWithData);
    }
}