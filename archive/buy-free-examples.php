<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
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
        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), Defaults::SALES_CHANNEL);

        $productId = '1a58fc09439a48d094d1b485fbe9d8e3';

        $lineItem = new LineItem($productId, "product", $productId, 1);
        $lineItem->setGood(false);
        $lineItem->setStackable(false);
        $lineItem->setRemovable(true);
        $lineItem->setReferencedId($productId);

        $service = $this->getContainer()->get(CartService::class);

        $cart = $service->getCart($context->getToken(), $context);

        $service->add($cart, [$lineItem], $context);

        $cart = $service->getCart($context->getToken(), $context);

        dd($cart);

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

