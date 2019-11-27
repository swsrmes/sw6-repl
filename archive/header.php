<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\Category\SalesChannel\NavigationRoute;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Storefront\Pagelet\Header\HeaderPagelet;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Main
{
    /**
     * @var ContainerInterface
     */
    private $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function run()
    {
        $salesChannelId = $this->c->get(Connection::class)
            ->fetchColumn("SELECT LOWER(HEX(sales_channel_id)) FROM sales_channel_translation WHERE name = 'Storefront'");

        $context= $this->c->get(SalesChannelContextFactory::class)
            ->create(Uuid::randomHex(), $salesChannelId);

        $result = $this->c->get(HeaderPageletLoader::class)
            ->load(new Request(), $context);
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

