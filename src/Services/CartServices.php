<?php
namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartServices{

    private $session;
    private $repoProduct;

    public function __construct(SessionInterface $session, ProductRepository $repoProduct)
    {
     $this->session   = $session;
     $this->repoProduct = $repoProduct;

    }

    public function addToCart($id){
        $cart = $this->getCart();
        if (isset ($cart[$id])) {
            //produit déja dans le panier
            $cart[$id]++;
        }else {
            //produit n'est pas dans le panier
            $cart[$id] = 1;
        }
        $this->updateCart($cart);
    }

    public function deleteFromCart($id){
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            //produit déja dans la panier
            if ($cart[$id] > 1) {
                //produit existe plus d'une fois
                $cart [$id]--;
            } else {
                //reterer le produit du panier 
                unset($cart[$id]);
            }
            //mettre à jours le session
            $this->updateCart($cart);
        }
    }

       public function deleteAllToCart($id){
             $cart = $this->getCart();
        if (isset($cart[$id])) {
            //produit déja dans la panier
           
                unset($cart[$id]);
            
            //mettre à jours le session
            $this->updateCart($cart);
        }
    }

       public function deleteCart(){
        $this->updateCart([]);
    }

       public function updateCart($cart){
           $this->session->set('cart', $cart);
    }

       public function getCart(){
        return $this->session->get('cart',[]);
    }

   


    
    public function getFullCart(){
        $cart = $this->getCart();
        $fullCart= [];
        $quantity_cart =0;
        $subTotal = 0;

        foreach ($cart as $id => $quantity) {
           $product = $this->repoProduct->find($id);
            if ($product) {
                //produit rcupéré avec succés
                //recupéré les donées et mettre dans un tableau
                $fullCart[]=[
                    "quantity" => $quantity,
                    "product" => $product,
                ];
             

            } else {
                //id incorrecte
                $this->deleteFromCart($id);
            }

        }
        return $fullCart;
    }


}


?>