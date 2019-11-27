<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\MessageQueue\Monitoring\AbstractMonitoringGateway;
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
        $gateway = $this->getContainer()->get('shopware.queue.monitoring.gateway');

        dump($this->getContainer()->getParameter('shopware.queue.monitoring.type'));

        /** @var AbstractMonitoringGateway $gateway */
        $all = $gateway->get();
        dd($all);

    }
}


$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

