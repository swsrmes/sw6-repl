<?php

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;

$container = require  __DIR__ . '/boot/boot.php';

$factory = $container->get(SalesChannelContextFactory::class);

$time = microtime(true);
for ($i = 0; $i < 20; $i++) {
    $context = $factory->create(Uuid::randomHex(), Defaults::SALES_CHANNEL);
}

dump((microtime(true)-$time));

