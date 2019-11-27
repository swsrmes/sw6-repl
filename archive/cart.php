<?php

/** @var \Symfony\Component\DependencyInjection\TaggedContainerInterface $container */

use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Cart\ProductLineItemFactory;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;

$container = require  __DIR__ . '/boot/boot.php';

$connection = $container->get(\Doctrine\DBAL\Connection::class);

$factory = $container->get(ProductLineItemFactory::class);

$context = $container->get(SalesChannelContextService::class)
    ->get(Defaults::SALES_CHANNEL, Uuid::randomHex());

$service = $container->get(CartService::class);

$cart = $service->getCart($context->getToken(), $context);

$ids = $connection->fetchAll('SELECT LOWER(HEX(id)) as id FROM product WHERE child_count = 0 OR parent_id IS NOT NULL ORDER BY RAND() LIMIT 500 ');
$ids = array_column($ids, 'id');

$time2 = microtime(true);

$count = 20;

for ($i = 0; $i < $count; $i++) {
    $index = array_rand($ids);
    $item = $factory->create($ids[$index]);
    $cart = $service->add($cart, $item, $context);
}

echo PHP_EOL . str_pad("(single) add {$count} items", 20) . " " . (microtime(true)-$time2);

$service->getCart($context->getToken(), $context, CartService::SALES_CHANNEL, false);

$time = microtime(true);
$cart = $service->getCart(Uuid::randomHex(), $context);

$items = [];
for ($i = 0; $i < $count; $i++) {
    $index = array_rand($ids);
    $items[] = $factory->create($ids[$index]);
}

$cart = $service->add($cart, $items, $context);

echo PHP_EOL . str_pad("(multip) add {$count} items", 20) . " " . (microtime(true)-$time);

