<?php

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Cart\ProductLineItemFactory;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\FetchModeHelper;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = require  __DIR__ . '/boot/boot.php';

/** @var CartService $service */
$service = $container->get(CartService::class);

$ids = $container
    ->get(Connection::class)
    ->fetchAll('SELECT LOWER(HEX(id)) as id FROM product LIMIT 20');

$ids = FetchModeHelper::groupUnique($ids);

if (empty($ids)) {
    throw new RuntimeException('No products in system');
}

$customerId = $container
    ->get(Connection::class)
    ->fetchColumn('SELECT LOWER(HEX(id)) FROM customer');

if (!$customerId) {
    throw new RuntimeException('No customer in system');
}

$items = $container
    ->get(ProductLineItemFactory::class)
    ->createList($ids);

$token = Uuid::randomHex();

$context = $container
    ->get(SalesChannelContextFactory::class)
    ->create($token, Defaults::SALES_CHANNEL, ['customerId' => $customerId]);

$cart = $service->getCart($token, $context);

$cart = $service->addList($cart, $items, $context);

$cart->setData(null);

$time = microtime(true);

$service->order($cart, $context);

dump((microtime(true)-$time));


