<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ShoppingCartId extends Id
{
    public static function generate()
    {
        return new static(uniqid('shoppingcart'));
    }
}
