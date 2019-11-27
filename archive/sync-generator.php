<?php

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */

use Doctrine\DBAL\Connection;
use Faker\Factory;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Sync\SyncBehavior;
use Shopware\Core\Framework\Api\Sync\SyncOperation;
use Shopware\Core\Framework\Api\Sync\SyncService;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\Demodata\Faker\Commerce;
use Shopware\Core\Framework\Uuid\Uuid;

$container = require __DIR__ . '/boot/boot.php';

$products = [];
$faker = Factory::create('de-DE');
$faker->addProvider(new Commerce($faker));
$faker->addProvider(new ImagesGeneratorProvider($faker));

function getIds(string $table, $container) {
    $ids = $container->get(Connection::class)
        ->fetchFirstColumn('SELECT LOWER(HEX(id)) as id FROM ' . $table);

    if (empty($ids)) {
        throw new RuntimeException($table . ' is empty');
    }
    return $ids;
}

$taxIds = getIds('tax', $container);
$categoryIds = getIds('category', $container);
$properties = getIds('property_group_option', $container);
$rules = getIds('rule', $container);
$mediaIds = getIds('media', $container);
$manufacturerIds = getIds('product_manufacturer', $container);

$categoryIds = array_map(function($id) {
    return ['id' => $id];
}, $categoryIds);

$properties = array_map(function($id) {
    return ['id' => $id];
}, $properties);

$mediaMapping = array_map(function($id) {
    return ['mediaId' => $id];
}, $mediaIds);

for ($i = 0; $i < 100; $i++) {
    $price = $faker->randomFloat(2, 20, 1000);

    $ruleIds = $faker->randomElements($rules, 5);

    $prices = [];
    foreach ($ruleIds as $ruleId) {
        $prices[] = [
            'quantityStart' => 1,
            'quantityEnd' => 3,
            'ruleId' => $ruleId,
            'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => $faker->randomFloat(2, 20, 1000), 'net' => $faker->randomFloat(2, 20, 1000), 'linked' => false]],
        ];

        $prices[] = [
            'quantityStart' => 4,
            'quantityEnd' => null,
            'ruleId' => $ruleId,
            'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => $faker->randomFloat(2, 20, 1000), 'net' => $faker->randomFloat(2, 20, 1000), 'linked' => false]],
        ];
    }

    $products[] = [
        'name' => $faker->productName,
        'active' => true,
        'productNumber' => Uuid::randomHex(),
        'coverId' => $faker->randomElement($mediaIds),
        'manufacturerId' => $faker->randomElement($manufacturerIds),
        'media' => $faker->randomElements($mediaMapping, 5),
        'stock' => 10,
        'price' => [
            ['currencyId' => Defaults::CURRENCY, 'gross' => $price, 'net' => $price, 'linked' => false]
        ],
        'prices' => $prices,
        'categories' => $faker->randomElements($categoryIds, 2),
        'properties' => $faker->randomElements($properties, 10),
        'taxId' => $faker->randomElement($taxIds)
    ];
}

$operations = [
    [
        'action' => SyncOperation::ACTION_UPSERT,
        'entity' => ProductDefinition::ENTITY_NAME,
        'payload' => $products
    ]
];

$objects = [];
foreach ($operations as $operation) {
    $objects[] = new SyncOperation(Uuid::randomHex(), $operation['entity'], $operation['action'], $operation['payload'], 3);
}

$behavior = new SyncBehavior(false, true, EntityIndexerRegistry::DISABLE_INDEXING);

$before = $container->get(Connection::class)
    ->fetchAllKeyValue("
        SELECT 'product' as `key`, COUNT(*) as `count` FROM product UNION
        SELECT 'product_price' as `key`, COUNT(*) as `count` FROM product_price UNION
        SELECT 'product_properties' as `key`, COUNT(*) as `count` FROM product_property UNION
        SELECT 'product_category' as `key`, COUNT(*) as `count` FROM product_category
    ");


$time = microtime(true);

$container->get(SyncService::class)
    ->sync($objects, \Shopware\Core\Framework\Context::createDefaultContext(), $behavior);

dump('sync call: ' . round(microtime(true)-$time, 5));

$after = $container->get(Connection::class)
    ->fetchAllKeyValue("
        SELECT 'product' as `key`, COUNT(*) as `count` FROM product UNION
        SELECT 'product_price' as `key`, COUNT(*) as `count` FROM product_price UNION
        SELECT 'product_properties' as `key`, COUNT(*) as `count` FROM product_property UNION
        SELECT 'product_category' as `key`, COUNT(*) as `count` FROM product_category
    ");

dump($before);
dump($after);

file_put_contents(__DIR__ . '/sync.json', json_encode($operations, JSON_PRESERVE_ZERO_FRACTION | JSON_PRETTY_PRINT));
echo PHP_EOL;
echo 'file updated: ' . __DIR__ . '/sync.json';
echo PHP_EOL;

