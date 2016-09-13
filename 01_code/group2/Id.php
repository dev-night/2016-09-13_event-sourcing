<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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