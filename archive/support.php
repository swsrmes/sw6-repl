<?php

namespace Script;

use Shopware\Core\Content\Test\Product\ProductBuilder;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Test\IdsCollection;
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
        $ids = new IdsCollection();

        $this->getContainer()->get('product.repository')->create(
            $this->createProducst($ids, ['red']),
            Context::createDefaultContext()
        );

        $this->getContainer()->get('product.repository')->create(
            $this->createProducst($ids, ['green']),
            Context::createDefaultContext()
        );

        $this->getContainer()->get('product.repository')->create(
            $this->createProducst($ids, ['green', 'red']),
            Context::createDefaultContext()
        );

        $criteria = new Criteria();
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
        $criteria->setLimit(0);
//        $criteria->addFilter(
//            new EqualsAnyFilter('properties.id', $ids->getList(['red', 'green']))
//        );
        $criteria->addFilter(
            new EqualsAnyFilter('propertyIds', $ids->getList(['red', 'green']))
        );

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), Defaults::SALES_CHANNEL);

        $result = $this->getContainer()->get('sales_channel.product.repository')->searchIds($criteria, $context);
        dd($result->getTotal());
    }

    private function createProducst(IdsCollection $ids, array $color)
    {
        $products = [];
        for ($i = 0; $i < 50; $i++) {
            $builder = (new ProductBuilder($ids, Uuid::randomHex()))
                ->price(100)
                ->visibility()
//                ->build()
            ;
            foreach ($color as $c) {
                $builder->property($c, 'color');
            }
            $products[] = $builder->build();


        }
        return $products;
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


