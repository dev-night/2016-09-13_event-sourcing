<?php

function __autoload($class)
{
    require_once $class . '.php';
}

$eventStore = new EventStore('store001.dat');

switch ($_POST['action']) {
    case 'create_shopping_cart':
        $event = new ShoppingCartWasCreated();
        
        echo $event->getShoppingCartId();
        break;
}




