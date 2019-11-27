<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Test\Cms\LayoutBuilder;
use Shopware\Core\Defaults;
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


        $products = $this->getContainer()->get(Connection::class)
            ->fetchFirstColumn('SELECT LOWER(HEX(id)) FROM product');

        foreach ($products as $index => $id) {
            $ids->set('product-' . $index, $id);
        }

        $key = (new \DateTime())->format('H:i:s');

        $builder = (new LayoutBuilder($ids, $key))
            ->productSlider('slider', $ids->getList(['product-1', 'product-2', 'product-3']))

        ;


        $builder->productThreeColumnBlock('boxes', [
            $builder->productBox('box-1', $ids->get('product-1')),
            $builder->productBox('box-2', $ids->get('product-2')),
            $builder->productBox('box-3', $ids->get('product-3'))
        ]);

        $this->getContainer()->get('cms_page.repository')
            ->create([$builder->build()], $ids->getContext());

        $this->getContainer()->get(Connection::class)->executeStatement(
            'UPDATE category SET cms_page_id = :id, cms_page_version_id = :version',
            ['id' => $ids->getBytes($key), 'version' => Uuid::fromHexToBytes(Defaults::LIVE_VERSION)]
        );
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

