<?php

namespace Script;

use Shopware\Core\Content\Property\PropertyGroupCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

$x = new Main(require  __DIR__ . '/boot/boot.php');

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
        $x = new Sorter();
        $x->sort(new EntityCollection());
    }
}

abstract class AbstractSorter
{
    /**
     * @deprecated v6.5.0.0 - parameter will be entity collection
     */
    abstract public function sort(PropertyGroupCollection $collection);
}

class Sorter extends AbstractSorter
{
    public function sort(EntityCollection $collection)
    {

    }
}

$x->run();

