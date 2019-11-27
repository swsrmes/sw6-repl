<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\HttpKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseScript
{
    public ContainerInterface $container;
    public Api $api;
    public StoreApi $store;
    public HttpKernel $kernel;

    public function __construct(HttpKernel $kernel)
    {
        $this->kernel = $kernel;
        $this->container = $kernel->getKernel()->getContainer();
        $this->store = new StoreApi($kernel);
        $this->api = new Api($this->container);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function fetchOne(string $query, array $parameters = [], array $types = [])
    {
        return $this->getContainer()->get(Connection::class)->fetchOne($query, $parameters, $types);
    }

    public function fetchAll(string $query, array $parameters = [], array $types = []): array
    {
        return $this->getContainer()->get(Connection::class)->fetchAllAssociative($query, $parameters, $types);
    }

    public function fetchColumn(string $query, array $parameters = [], array $types = []): array
    {
        return $this->getContainer()->get(Connection::class)->fetchFirstColumn($query, $parameters, $types);
    }

    public function fetchRow(string $query, array $parameters = [], array $types = []): array
    {
        return $this->getContainer()->get(Connection::class)->fetchAssociative($query, $parameters, $types);
    }
}