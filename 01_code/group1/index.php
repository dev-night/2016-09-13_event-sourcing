<?php
$writer1 = new Writer(1);
$writer3 = new Writer(3);
$writer = new Writer(2);
$writer3->eventAddToCart(new Product(1, 'Der Gerät1'));
$writer3->eventAddToCart(new Product(1, 'Der Gerät1'));
$writer3->eventAddToCart(new Product(2, 'Der Gerät2'));
$writer->eventAddToCart(new Product(4, 'Der Gerät4'));
$writer->eventAddToCart(new Product(5, 'Der Gerät5'));
$writer1->eventAddToCart(new Product(2, 'Der Gerät2'));
$writer3->eventAddToCart(new Product(3, 'Der Gerät3'));
$writer3->eventAddToCart(new Product(4, 'Der Gerät4'));
$writer3->eventAddToCart(new Product(5, 'Der Gerät5'));
$writer3->eventAddToCart(new Product(6, 'Der Gerät6'));
$writer1->eventAddToCart(new Product(1, 'Der Gerät1'));
$writer->eventAddToCart(new Product(1, 'Der Gerät1'));
$writer->eventAddToCart(new Product(1, 'Der Gerät1'));
$writer->eventAddToCart(new Product(2, 'Der Gerät2'));
$writer->eventAddToCart(new Product(3, 'Der Gerät3'));
$writer->eventDeleteFromCart(new Product(3, 'Der Gerät3'));
$writer->eventAddToCart(new Product(6, 'Der Gerät6'));
$reader = new Reader();
$reader->displayEndResult();
$reader->displayMaxResult();

class EventBus
{
	private $registerHandle = array();

	public function register($handler)
	{
		$this->registerHandle[get_class($handler)] = $handler;
	}

	public function drive()
	{
		$events = EventStore::getEvents();

		foreach($events as $event)
		{
			$this->registerHandle[$event->type]->run($event);
		}
	}
}

class Reader
{
	private $events = array();

	public function __construct()
	{
		return EventStore::getEvents();
	}

	public function pushEvents($events)
	{
		$this->events = $events;
	}

	public function buildResultArray()
	{
		$resultArray = array();

		foreach($this->events as $key => $event)
		{
			$cartID = $event[1]->getCartID();
			switch($event['action'])
			{
				case 'add':
					$resultArray[$cartID][$event[2]->getProductID()][] = $event[2];
					break;
				case 'delete':
					array_shift($resultArray[$cartID][$event[2]->getProductID()]);
					break;
			}
		}

		return $resultArray;
	}

	public function displayEndResult()
	{
		$resultArray = $this->buildResultArray();

		foreach ($resultArray as $cartID => $products)
		{
			var_dump($cartID, $products);
		}
	}

	public function displayMaxResult()
	{
		$resultArray = $this->buildResultArray();
		$maxValue = 0;
		$maxID = 0;

		foreach ($resultArray as $cartID => $products)
		{
			if(sizeof($products) > $maxValue)
			{
				$maxID = $cartID;
				$maxValue = sizeof($products);
			}
		}
	}
}

final class EventStore
{
	private static $events = array();

	public static function addEvent($event)
	{
		static::$events[] = $event;
//		EventBus::publishEvents();
	}

	public static function getEvents()
	{
		return static::$events;
	}
}

class Writer
{
	private $cartID;

	public function __construct($cartID)
	{
		$this->cartID = $cartID;
	}

	/**
	 * @return int
	 */
	public function getCartID()
	{
		return $this->cartID;
	}

	public function eventAddToCart($product)
	{
		EventStore::addEvent(array('action' => 'add', $this, $product));
	}

	public function eventDeleteFromCart($product)
	{
		EventStore::addEvent(array('action' => 'delete', $this, $product));
	}
}

final class Product
{
	/**
	 * @var int $pID
	 */
	private $pID;

	/**
	 * @var string $name
	 */
	private $name;

	public function __construct($pID, $name)
	{
		$this->pID = $pID;
		$this->name =$name;
	}

	/**
	 * @return int
	 */
	public function getPID()
	{
		return $this->pID;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
}