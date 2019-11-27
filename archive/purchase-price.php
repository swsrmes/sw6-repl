<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
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
        $connection = $this->c->get(Connection::class);

        // Convert any existing purchase price values into the new 'purchase_prices' JSON field
        $defaultCurrencyId = Defaults::CURRENCY;
        $connection->executeUpdate(
            'UPDATE `product`
            LEFT JOIN tax ON product.tax = tax.id
            SET purchase_prices = IF(
                purchase_price IS NULL,
                NULL,
                JSON_OBJECT(
                    :currencyKey, JSON_OBJECT(
                        "net", `product`.`purchase_price` / (1 + (`tax`.`tax_rate` / 100)),
                        "gross", `product`.`purchase_price`,
                        "linked", "true",
                        "currencyId", :currencyId
                    )
                )
            );',
            [
                'currencyKey' => sprintf('c%s', $defaultCurrencyId),
                'currencyId' => $defaultCurrencyId,
            ]
        );
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();
