<?php

namespace Script;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Main
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function run()
    {
        $x = [];
        $this->xx($x);
        dd($x);
    }

    private function xx(array &$array)
    {
        $array[] = 1;
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

