<?php declare(strict_types=1);

use Shopware\Core\Checkout\Cart\CartRuleLoader;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MaxAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\MinAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = require __DIR__ . '/boot/boot.php';

$sql = '
    DROP TABLE IF EXISTS `test_price`;

    CREATE TABLE `test_price` (
      `id` binary(16) NOT NULL,
      `number` varchar(50) NOT NULL,
      `prices` json NOT NULL
    );
';

$all = [];



$function = '
DROP FUNCTION IF EXISTS cheapest_price;

CREATE FUNCTION cheapest_price (s CHAR(20)) RETURNS CHAR(50) DETERMINISTIC
RETURN CAST(1.33 AS DECIMAL(30, 20));
';

$db = $container->get(\Doctrine\DBAL\Connection::class);
$db->executeUpdate($function);

$f = $db->fetchAll("SELECT cheapest_price(number) as myname FROM test_price");
dd($f);


for ($i = 0; $i < 10; $i++) {
    $prices = [
        [ 'variant' => 1, 'rule' => 'a', 'price' => random_int(10, 40) ],
        [ 'variant' => 1, 'rule' => 'b', 'price' => random_int(10, 40) ],
        [ 'variant' => 2, 'rule' => 'c', 'price' => random_int(10, 40) ],
        [ 'variant' => 3, 'rule' => 'a', 'price' => random_int(10, 40) ],
        [ 'variant' => null, 'rule' => 'c', 'price' => random_int(10, 40)]
    ];

    $data = [
        'id' => Uuid::randomBytes(),
        'number' => 'product ' . $i,
        'prices' => json_encode($prices)
    ];

    $all[] = $data;

    $container->get(\Doctrine\DBAL\Connection::class)
        ->insert('test_price', $data);
}


$sql = 'SELECT cheapest_price(price) as cheapest_price FROM test_price';
$x = $db->fetchAll($sql);
dd($x);
