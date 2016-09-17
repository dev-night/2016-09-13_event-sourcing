<?php

abstract class Id
{
    private $uuid;

    protected function __construct($uuid)
    {
        $this->uuid = $uuid;
    }
    
    public static function fromString($uuid)
    {
        return new static($uuid);
    }
    
    public function getUuid()
    {
        return $this->uuid;
    }
    
    public function __toString() {
        return $this->uuid;
    }
}

class ShoppingCartId extends Id
{
    public static function generate()
    {
        return new static(uniqid('shoppingcart'));
    }
}

class ProductId extends Id { }




class ShoppingCart
{
    private $shoppingCartId;
    private $recordedEvents = [];
    
    private function __construct(ShoppingCartId $shoppingCartId)
    {
        $this->shoppingCartId = $shoppingCartId;
    }
    
    public static function create(ShoppingCartId $shoppingCartId)
    {
        $shoppingCart = new static($shoppingCartId);
        $shoppingCart->recordEvent(new ShoppingCartWasCreated($shoppingCartId));
        return $shoppingCart;
    }
    
    private function recordEvent($event)
    {
        $this->recordedEvents[] = $event;
    }
    
    public function getRecordedEvents()
    {
        return $this->recordedEvents;
    }
    
    public function addProduct(ProductId $productId)
    {
        $this->recordEvent(new ProductAddedToShoppingCart($this->shoppingCartId, $productId));
    }
    
    public function removeProduct(ProductId $productId)
    {
        $this->recordEvent(new ProductRemovedFromShoppingCart($this->shoppingCartId, $productId));
    }
    
}

class ProductAddedToShoppingCart
{
    private $shoppingCartId;
    private $productId;
    
    public function __construct(ShoppingCartId $shoppingCartId, ProductId $productId)
    {
        $this->shoppingCartId = $shoppingCartId;
        $this->productId = $productId;
    }
}

class ProductRemovedFromShoppingCart
{
    private $shoppingCartId;
    private $productId;
    
    public function __construct(ShoppingCartId $shoppingCartId, ProductId $productId)
    {
        $this->shoppingCartId = $shoppingCartId;
        $this->productId = $productId;
    }
}

class ShoppingCartWasCreated
{
    private $shoppingCartId;
    
    public function __construct(ShoppingCartId $shoppingCartId)
    {
        $this->shoppingCartId = $shoppingCartId;
    }
}



class ShoppingCartRepository
{
    private $eventStore;
    
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }
    
    public function persist(ShoppingCart $shoppingCart)
    {
        $this->eventStore->persist($shoppingCart->getRecordedEvents());
    }
    
}

class EventStore
{
    private $events = [];
    private $eventBus;
    
    public function __construct($eventBus)
    {
        $this->eventBus = $eventBus;
    }
    
    public function persist(array $events)
    {
        foreach($events as $event)
        {
            // $this->dbal->insert( .... )
            $this->events[] = $event;
            $this->eventBus->publish($event);
        }
    }
    
}


class EventBus
{
    private $subscribers = [];
    
    public function addSubscriber($subscriber)
    {
        $this->subscribers[] = $subscriber;
    }
    
    public function publish($event)
    {
        $handlerName = "handle" . get_class($event);
        
        foreach ($this->subscribers as $subscriber) {
            if (method_exists($subscriber, $handlerName)) {
                $subscriber->$handlerName($event);
            }
        }
    }
}


class NumberOfCreatedShoppingCartsProjector
{
    private $counter = 0;
    
    public function handleShoppingCartWasCreated($event)
    {
        $this->counter ++;
    }
    
    public function getCounter()
    {
        return $this->counter;
    }
}

$shoppingCartId = ShoppingCartId::generate();
$shoppingCart = ShoppingCart::create($shoppingCartId);

$_SESSION['shopping_cart_id'] = $shoppingCartId;


$redProduct = ProductId::fromString('08/15-red');

$shoppingCart->addProduct($redProduct);
$shoppingCart->removeProduct($redProduct);



$eventBus = new EventBus();

$es = new EventStore($eventBus);
$shoppingCartRepo = new ShoppingCartRepository($es);

$projector = new NumberOfCreatedShoppingCartsProjector();
$eventBus->addSubscriber($projector);


$shoppingCartRepo->persist($shoppingCart); 


var_dump($projector->getCounter());
