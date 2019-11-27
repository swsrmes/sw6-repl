<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
        $this->getContainer()->get(Connection::class)
            ->executeStatement('DELETE FROM custom_entity_blog');

        $id = $this->getContainer()->get(Connection::class)
            ->fetchOne('SELECT LOWER(HEX(id)) FROM product');

        $blog = [
            'position' => 1,
            'rating' => 2.2,
            'title' => 'Test',
            'content' => 'Test <123>',
            'display' => true,
            'payload' => ['foo' => 'Bar'],
            'email' => 'test@test.com',
            'topSellerId' => $id,
            'comments' => [
                ['title' => 'test', 'content' => 'test', 'email' => 'test@test.com'],
                ['title' => 'test', 'content' => 'test', 'email' => 'test@test.com'],
                ['title' => 'test', 'content' => 'test', 'email' => 'test@test.com'],
                ['title' => 'test', 'content' => 'test', 'email' => 'test@test.com'],
            ]
        ];

        /** @var EntityRepositoryInterface $repo */
        $repo = $this->getContainer()->get('custom_entity_blog.repository');
        $repo->create([$blog], Context::createDefaultContext());

        $criteria = new Criteria();
        $criteria->addAssociation('comments');
        $criteria->addAssociation('topSeller');

        $x = $repo
            ->search($criteria, Context::createDefaultContext())
            ->first();

        dd($x);
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

