<?php

namespace Script;

use Shopware\Core\Content\Category\SalesChannel\CategoryRoute;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
        $cache = $this->container->get(CategoryRoute::class);

        $cache = $cache->cache;

        $key = 'key' . Uuid::randomHex();

        $item = $cache->get($key, function(ItemInterface $item) {
            $item->tag(['a', 'b']);
            $item->expiresAfter(10);

            return 'WUSEL';
        });

        echo $item;
    }

}

$env = 'dev';

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

