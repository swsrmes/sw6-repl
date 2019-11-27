<?php

class Entity
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId() { return $this->id; }

    public function __get($name)
    {
        return $this->$name;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}


$x = new Entity(1);

$x->id = 10;
echo $x->id . PHP_EOL;

