<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ShoppingCartWasCreated extends Event
{
    protected $shoppingCartId;

    public function __construct() {
        $shoppingCart = new ShoppingCart(new ShoppingCartId());
        $this->shoppingCartId = $shoppingCart->getId();
    }

    public function getShoppingCartId()
    {
        return $this->shoppingCartId;
    }
}
