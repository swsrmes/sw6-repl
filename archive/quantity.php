<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Content\Test\Product\ProductBuilder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\IdsCollection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelEntityLoadedEvent;
use Shopware\Core\Test\TestDefaults;
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
        $this->getContainer()->get(Connection::class)->executeStatement('DELETE FROM rule WHERE name = :x', ['x' => 'test']);

        $product = (new ProductBuilder($ids, 'p1'))
            ->visibility()
            ->price(1)
            ->prices('rule-2', 200, 'default', null, 40);

        $this->getContainer()->get('product.repository')->create([$product->build()], Context::createDefaultContext());

        $context = $this->getContainer()->get(SalesChannelContextFactory::class)->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);
        $context->setRuleIds([$ids->get('rule-2')]);

        /** @var SalesChannelProductEntity $product */
        $product = $this->getContainer()->get('sales_channel.product.repository')
            ->search(new Criteria([$ids->get('p1')]), $context)
            ->first();

        dd($product->getCalculatedPrices());
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

