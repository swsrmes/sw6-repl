<?php

namespace Script;

use Doctrine\DBAL\Connection;
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
        $schema = $this->getContainer()->get(Connection::class)
            ->getSchemaManager()->createSchema();
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

