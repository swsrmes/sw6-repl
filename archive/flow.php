<?php

namespace Script;

use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Content\Flow\Dispatching\FlowLoader;
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
        $loader = $this->getContainer()->get(FlowLoader::class);

        $time = microtime(true);
        $x = $loader->load(CheckoutOrderPlacedEvent::EVENT_NAME, Context::createDefaultContext());
        error_log(sprintf('%s: time[%s]', str_pad("x", 20), round(microtime(true)-$time, 5)) . "\n", 3, '/var/www/html/sw6/var/log/test.log');

        dd($x);
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

