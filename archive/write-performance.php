<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Faker\Factory;
use Shopware\Core\Content\Product\DataAbstractionLayer\ProductIndexer;
use Shopware\Core\Content\Product\DataAbstractionLayer\ProductIndexingMessage;
use Shopware\Core\Content\Test\Product\ProductBuilder;
use Shopware\Core\Framework\Api\Sync\SyncBehavior;
use Shopware\Core\Framework\Api\Sync\SyncOperation;
use Shopware\Core\Framework\Api\Sync\SyncService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\Test\IdsCollection;
use Shopware\Core\Framework\Uuid\Uuid;
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
        $ids = $this->getContainer()->get(Connection::class)
            ->fetchFirstColumn('SELECT LOWER(HEX(id)) FROM product WHERE parent_id IS NULL');

        $message = new ProductIndexingMessage(['9c8f69f40ae14396a01e8cf6d38d6d77'], null, Context::createDefaultContext());
        $this->getContainer()->get(ProductIndexer::class)->handle($message);

        return;

        foreach ($ids as $id) {
            $message = new ProductIndexingMessage([$id], null, Context::createDefaultContext());

            $this->getContainer()->get(ProductIndexer::class)->handle($message);
        }
    }
}

\ini_set('memory_limit', '-1');

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

