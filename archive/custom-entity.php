<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
        $connection = $this->getContainer()->get(Connection::class);

        $connection->executeStatement('
        DROP TABLE IF EXISTS custom_entity_receipt;

        CREATE TABLE `custom_entity_receipt` (
            `id` binary(16) NOT NULL,
            `title` varchar(255) NOT NULL,
            `description` longtext NOT NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

        DROP TABLE IF EXISTS custom_entity_blog;

        CREATE TABLE `custom_entity_blog` (
            `id` binary(16) NOT NULL,
            `title` varchar(255) NOT NULL,
            `description` longtext NOT NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ');

        $insert = new MultiInsertQueryQueue($connection);
        for ($i = 0; $i <= 500; $i++) {
            $insert->addInsert('custom_entity_receipt', ['id' => Uuid::randomBytes(), 'title' => 'Example', 'description' => 'example']);
            $insert->addInsert('custom_entity_blog', ['id' => Uuid::randomBytes(), 'title' => 'Example', 'description' => 'example']);
        }
        $insert->execute();
        $criteria = new Criteria();
        $criteria->setLimit(2);

        $entities = $this->getContainer()->get('custom_entity_blog.repository')
            ->search($criteria, Context::createDefaultContext());

        dd($entities);
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

