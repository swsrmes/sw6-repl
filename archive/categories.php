<?php

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container = require __DIR__ . '/boot/boot.php';

$data = [
    [
        'name' => 'Navigation #1',
        'children' => [
            ['name' => 'A', 'children' => [
                ['name' => 'B', 'children' => [
                    ['name' => 'C', 'children' => [
                        ['name' => 'D', 'children' => [
                            ['name' => 'E', 'children' => [

                            ]],
                        ]],
                    ]],
                ]],
            ]],
            ['name' => 'A2', 'children' => [
                ['name' => 'B', 'children' => [
                    ['name' => 'C', 'children' => [
                        ['name' => 'D', 'children' => [
                            ['name' => 'E', 'children' => [

                            ]],
                        ]],
                    ]],
                ]],
            ]],
            ['name' => 'A3', 'children' => [
                ['name' => 'B', 'children' => [
                    ['name' => 'C', 'children' => [
                        ['name' => 'D', 'children' => [
                            ['name' => 'E', 'children' => [

                            ]],
                        ]],
                    ]],
                ]],
            ]],
        ]
    ],
    [
        'name' => 'Navigation #2',
        'children' => [
            ['name' => 'test1'],
            ['name' => 'test2'],
            ['name' => 'test3'],
            ['name' => 'test4']
        ]
    ],
    [
        'name' => 'Footer #1',
        'children' => [
            ['name' => 'Information #1', 'children' => [
                ['name' => 'Kontakt'],
                ['name' => 'Impressum'],
                ['name' => 'Newsletter']
            ]],
            ['name' => 'Service #1', 'children' => [
                ['name' => 'Hilfe'],
                ['name' => 'FAQ'],
                ['name' => 'Rückgabe']
            ]]
        ]
    ],
    [
        'name' => 'Footer #2',
        'children' => [
            ['name' => 'Information #1', 'children' => [
                ['name' => 'Kontakt'],
                ['name' => 'Impressum'],
                ['name' => 'Newsletter']
            ]],
            ['name' => 'Service #2', 'children' => [
                ['name' => 'Hilfe'],
                ['name' => 'FAQ'],
                ['name' => 'Rückgabe']
            ]]
        ]
    ]
];

$container->get('category.repository')->create($data, \Shopware\Core\Framework\Context::createDefaultContext());

$connection = $container->get(\Doctrine\DBAL\Connection::class);

$connection->executeUpdate('UPDATE category SET active = 1');
$connection->executeUpdate("UPDATE category SET cms_page_id = UNHEX('0980dab35ef34f46a8c4c6e48961a672')");

