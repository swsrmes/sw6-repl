<?php

namespace Script;

use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaCollection;
use Shopware\Core\Content\Product\Aggregate\ProductMedia\ProductMediaEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\FieldSerializer\JsonFieldSerializer;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\Price;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Tax\TaxEntity;
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
        $criteria = new Criteria();
        $criteria->addAssociation('cover');
        $criteria->addAssociation('media');

        $product = $this->getContainer()->get('product.repository')
            ->search($criteria, Context::createDefaultContext())->first();

        /** @var ProductEntity $product */
        if ($product->getMedia() !== null) {

            $product->getMedia()->sort(function (ProductMediaEntity $a, ProductMediaEntity $b) {
                return $a->getPosition() <=> $b->getPosition();
            });

            if ($product->getCover()) {
                $product->setMedia(new ProductMediaCollection(\array_merge(
                    [$product->getCover()->getId() => $product->getCover()],
                    $product->getMedia()->getElements()
                )));
            }

            dump($product->getCoverId());
            dd($product->getMedia()->getKeys());

        }
    }
}

$env = 'dev';

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

