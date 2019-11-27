<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Test\Product\ProductBuilder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\StatsAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Test\IdsCollection;
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
        $ids = new IdsCollection();

        $this->getContainer()->get(Connection::class)->executeStatement('DELETE FROM product');

        $products = [
            (new ProductBuilder($ids, 'p1'))
                ->name('foo')
                ->price(100)
                ->build(),

            (new ProductBuilder($ids, 'p2'))
                ->name('match')
                ->price(200)
                ->build(),

            (new ProductBuilder($ids, 'p3'))
                ->name('match')
                ->price(300)
                ->build(),
        ];


        $this->getContainer()->get('product.repository')->create($products, Context::createDefaultContext());

        $criteria = new Criteria();
        $criteria->setTerm('match');

        $criteria->addAggregation(new StatsAggregation('price', 'product.price'));

        $x = $this->getContainer()->get('product.repository')
            ->search($criteria, Context::createDefaultContext());

        dump($ids->all());
        dump($x->getIds());
        dump($x->getAggregations());

        dd();
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

