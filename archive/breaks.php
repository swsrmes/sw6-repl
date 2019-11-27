<?php

use Shopware\Core\Framework\Feature;
use Shopware\Core\Framework\Test\DependencyInjection\fixtures\TestEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

require  __DIR__ . '/boot/boot.php';


// version of release v6.4.0.0
class Service
{
    private EventDispatcherInterface $dispatcher;

    // service is not decorate-able
    public function foo(): int
    {
        $this->dispatcher->dispatch(new ExampleEvent());

        return 1;
    }
}

class Caller
{
    private Service $service;

    public function someAction()
    {
        $this->service->foo();
    }
}



############################




// version of release v6.4.1.0
class Service
{
    public function foo(): int
    {
        return 1;
    }
}

class Replacement
{
    public function bar(): int
    {
        return 2;
    }
}

$service = new Service();
$service->foo();



$f = new Test2();
$f->hello();



