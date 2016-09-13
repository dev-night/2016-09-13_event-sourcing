<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ShoppingCart
{
    private $id;
    
    public function __construct(ShoppingCartId $shoppingCartId)
    {
        $this->id = $shoppingCartId;
    }
    
    public function getId()
    {
        return $this->id;
    }
}
