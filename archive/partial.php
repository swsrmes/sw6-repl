<?php

namespace Script;

use Doctrine\DBAL\Connection;
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
        $sql = <<<SQL
FROM product p ORDER BY sales LIMIT 20
SQL;

        $time = microtime(true);
        $this->getContainer()->get(Connection::class)->fetchAllAssociative('SELECT id ' . $sql);
        echo sprintf('%s: time[%s]', str_pad("fetch", 20), round(microtime(true)-$time, 5)) . \PHP_EOL;

        $time = microtime(true);
        $this->getContainer()->get(Connection::class)->fetchAllAssociative('SELECT * ' . $sql);
        echo sprintf('%s: time[%s]', str_pad("fetch", 20), round(microtime(true)-$time, 5));






//        $criteria = new Criteria();
//
//        $criteria->addFields(['id', 'productNumber', 'categories.id']);
//
//        $criteria->setLimit(100);
//
//        $time = microtime(true);
//
//
//        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
//            ->create(Uuid::randomHex(), Defaults::SALES_CHANNEL);
//
//        $product = $this->getContainer()->get('sales_channel.product.repository')
//            ->search($criteria, $context);
//
//
//        echo sprintf('%s: time[%s]', str_pad("loading " . $criteria->getLimit() . " products", 20), round(microtime(true)-$time, 5));


//        $time = microtime(true);
//        $criteria = new Criteria();
//
//        $criteria->addFields(['id', 'name']);
//
//        $this->getContainer()->get('category.repository')->search($criteria, Context::createDefaultContext());
//
//        echo sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true)-$time, 5));


    }

}

$env = 'dev';

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


