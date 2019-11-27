<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
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
//        $this->getContainer()->get(Connection::class)
//            ->executeStatement('UPDATE `order_customer` SET customer_id = (SELECT id FROM customer LIMIT 1)');

//        $this->getContainer()
//            ->get(Connection::class)
//            ->executeStatement("UPDATE `order` SET state_id = (SELECT id FROM state_machine_state WHERE technical_name = 'completed')");

        $id = $this->getContainer()->get(Connection::class)
            ->fetchOne('SELECT LOWER(HEX(id)) FROM `order`');

        $payload = [
            'id' => $id,
            'customerComment' => Uuid::randomHex()
        ];

        $this->getContainer()->get('order.repository')->update([$payload], Context::createDefaultContext());
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

