<?php

namespace Script;

use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $keys = [
            SalesChannelContextService::CURRENCY_ID => true,
            SalesChannelContextService::LANGUAGE_ID => true,
            SalesChannelContextService::DOMAIN_ID => true,
            SalesChannelContextService::PAYMENT_METHOD_ID => true,
            SalesChannelContextService::SHIPPING_METHOD_ID => true,
            SalesChannelContextService::VERSION_ID => true,
            SalesChannelContextService::COUNTRY_ID => true,
            SalesChannelContextService::COUNTRY_STATE_ID => true,
        ];

        $options = [
            SalesChannelContextService::CURRENCY_ID => 'a',
            SalesChannelContextService::LANGUAGE_ID => 'b',
            'wusel' => 'c',
        ];

        $x = \array_intersect_key($options, $keys);
        print_r($x);
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();
