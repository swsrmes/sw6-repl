<?php

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;

$container = require  __DIR__ . '/boot/boot.php';


$data = [];

$c = $container->get(Connection::class);

create($c, 'role-1', [
    'product:read',
    'product:write',
    'category:read'
]);

create($c, 'role-2', [
    'product:read',
    'user:create'
]);

function create(Connection $c, string $name, array $privs) {
    $id = Uuid::randomBytes();

    $c->insert('acl_role', [
        'id' => $id,
        'name' => $name,
        'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_FORMAT),
        'privileges' => json_encode($privs)
    ]);

    $c->insert('acl_user_role', [
        'user_id' => Uuid::fromHexToBytes('6babdb5d3d6246b99e168d6e08a686bb'),
        'acl_role_id' => $id,
        'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_FORMAT),
    ]);

//    foreach ($privs as $priv) {
//        $priv = explode(':', $priv);
//
//        $c->insert('acl_resource', [
//            'acl_role_id' => $id,
//            'resource' => $priv[0],
//            'privilege' => $priv[1],
//            'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_FORMAT)
//        ]);
//    }
}
