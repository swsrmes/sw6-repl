<?php

/** @var \Symfony\Component\DependencyInjection\TaggedContainerInterface $container */

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldTypes;

$container = require  __DIR__ . '/boot/boot.php';

$container->get(\Doctrine\DBAL\Connection::class)
    ->executeUpdate('DELETE FROM custom_field');

$fields = [];
$payload = [];
$count = 40000;
for ($i = 0; $i < $count; $i++) {
    $fields[] = [
        'name' => 'field' . $i,
        'type' => CustomFieldTypes::INT
    ];
    $payload['field' . $i] = random_int(0, 2000);
}

$payload = array_slice($payload, random_int(50, 200), 1000, true);

$context = \Shopware\Core\Framework\Context::createDefaultContext();

$container->get('custom_field.repository')
    ->create($fields, $context);

error_log(print_r("### testing with " . $count . " custom fields ", true) . "\n", 3, '/var/log/test.log');

$data = [
    'id' => Uuid::randomHex(),
    'name' => 'test',
    'productNumber' => Uuid::randomHex(),
    'stock' => 10,
    'price' => [
        ['currencyId' => Defaults::CURRENCY, 'gross' => 15, 'net' => 10, 'linked' => false],
    ],
    'tax' => ['name' => 'test', 'taxRate' => 15],
    'customFields' => $payload
];

$time = microtime(true);

$container->get('product.repository')
    ->create([$data], $context);

error_log(str_pad("writing product", 20) . " " . (microtime(true)-$time) . "\n", 3, '/var/log/test.log');



