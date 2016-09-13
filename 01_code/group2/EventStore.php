<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class EventStore
{
    private static $instances = [];
    
    private $events = [];
    private $filename;
    
    public function __construct($filename) {
        $this->filename = $filename;
        $this->loadFromPersistence();
        self::$instances[] = $this;
    }
    
    public function __destruct() {
        $this->storeToPersistence();
    }

    private function loadFromPersistence()
    {
        $this->events = unserialize(file_get_contents($this->filename));
    }
    
    private function storeToPersistence()
    {
        file_put_contents($this->filename, serialize($this->events));
    }
    
    public static function addEvent(Event $event)
    {
        
        $this->events[] = $event;
    }
}