<?php

use Shopware\Core\Content\Category\Service\NavigationLoader;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Symfony\Component\Cache\CacheItem;

$container = require  __DIR__ . '/boot/boot.php';

$cache = $container->get(\Shopware\Core\Framework\Adapter\Cache\CacheDecorator::class);

$tags = ['a', 'b', 'c', 'd', 'e', 'f'];

/** @var \Shopware\Core\Framework\Adapter\Cache\CacheDecorator $cache */
for ($i = 0; $i < 10000; $i++) {
    $item = $cache->getItem('item-' . $i);
    $item->set($i);
    $cache->save($item);
}

for ($i = 0; $i < 10000; $i++) {
    $item = $cache->getItem('item-' . $i);
}


