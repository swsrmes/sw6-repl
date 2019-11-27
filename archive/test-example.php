<?php

namespace Script;

use Shopware\Core\Content\Product\DataAbstractionLayer\ProductIndexer;
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
        $indexer = $this->getContainer()->get(ProductIndexer::class);
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

