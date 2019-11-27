<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Cart\CartRuleLoader;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Cart\ProductLineItemFactory;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
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
        $loader = $this->getContainer()->get(CartRuleLoader::class);

        $context = $this->getContainer()->get(SalesChannelContextService::class)
            ->get(new SalesChannelContextServiceParameters(Defaults::SALES_CHANNEL, Uuid::randomHex()));

        $cart = $loader->loadByToken($context, $context->getToken())->getCart();

        $id = $this->getContainer()->get(Connection::class)->fetchOne('SELECT LOWER(HEX(id)) FROM product LIMIT 1');

        $lineItem = $this->getContainer()->get(ProductLineItemFactory::class)
            ->create($id);

        $this->getContainer()->get(CartService::class)
            ->add($cart, $lineItem, $context);

        $time = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            $cart = $this->getContainer()->get(CartService::class)
                ->getCart($context->getToken(), $context, CartService::SALES_CHANNEL, false);
        }

        echo sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true)-$time, 5));
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

