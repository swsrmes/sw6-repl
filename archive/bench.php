<?php

namespace Script;

use Shopware\Core\Content\Product\DataAbstractionLayer\StockUpdater;
use Shopware\Core\Framework\Context;
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
        $updater = $this->getContainer()->get(StockUpdater::class);

        $ids = ['19b6ec99c7804d9c97cfb9044510a0c2'];
        $time = microtime(true);
        $updater->update($ids, Context::createDefaultContext());
        echo sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true)-$time, 5));


    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

