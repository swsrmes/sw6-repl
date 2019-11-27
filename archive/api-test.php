<?php

namespace Script;

use Shopware\Core\Content\Product\ProductEntity;
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

        $time = microtime(true);

        for ($i = 0; $i < 2000000; $i++) {
            $x = new ProductEntity();
        }

        echo sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true) - $time, 5));
        echo PHP_EOL;


        $time = microtime(true);
        $blueprint = new ProductEntity();
        for ($i = 0; $i < 2000000; $i++) {
            $x = clone $blueprint;
        }

        echo sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true) - $time, 5));
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

