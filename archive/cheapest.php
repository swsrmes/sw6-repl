<?php

namespace Script;

use Shopware\Core\Content\Test\Product\ProductBuilder;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
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
        $ids = new IdsCollection();
        $x = (new ProductBuilder($ids, Uuid::randomHex()))
                ->price(80)
                ->visibility()
                ->variant(
                    (new ProductBuilder($ids, Uuid::randomHex()))
                        ->build()
                )
                ->variant(
                    (new ProductBuilder($ids, Uuid::randomHex()))
                        ->price(79)
                        ->price(88, null, 'currency')
                        ->prices('rule-a', 140)
                        ->prices('rule-a', 130, 'default', null, 3)
                        ->prices('rule-a', 188, 'currency')
                        ->prices('rule-a', 177, 'currency', null, 3)
                        ->build()
                )
                ->build();

        $this->getContainer()->get('product.repository')->create([$x], Context::createDefaultContext());


        $container = [
            'variant-1' => [
                'rule-1' => 1,
                'rule-2' => 2,
                'default' => 1
            ],

            'variant-2' => [
                'rule-2' => 2,
                'default' => 1
            ],

            'variant-3' => [
                'rule-1' => 1,
                'rule-2' => 2,
                'default' => null
            ],

            'variant-4' => [
                'default' => null
            ],
        ];

        $hasAdvancedPrices = count($container['variant-1']) > 1;

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

