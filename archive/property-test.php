<?php

class A
{
    public $a;
    public $b;
    public $c;
}

class B
{
    protected $a;
    protected $b;
    protected $c;

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __isset($name)
    {
        return isset($this->$name);
    }
}


class C
{
    protected $a;
    protected $b;
    protected $c;

    public function assign(array $options)
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
    }
}


$time = microtime(true);
$a = new A();
$count = 2000000;
for ($i = 0; $i < $count; $i++) {
    $a->a = $i;
    $a->b = $i;
    $a->c = $i;
}
echo sprintf('%s: time[%s]', str_pad("A", 20), round(microtime(true)-$time, 5)) . PHP_EOL;


$time = microtime(true);
$b = new B();
for ($i = 0; $i < $count; $i++) {
    $b->a = $i;
    $b->b = $i;
    $b->c = $i;
}

echo sprintf('%s: time[%s]', str_pad("b", 20), round(microtime(true)-$time, 5)) . PHP_EOL;



$time = microtime(true);
$c = new C();
for ($i = 0; $i < $count; $i++) {
    $c->assign([
        'a' => $i,
        'b' => $i,
        'c' => $i,
    ]);
}

echo sprintf('%s: time[%s]', str_pad("c", 20), round(microtime(true)-$time, 5)) . PHP_EOL;



