<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginLifecycleService;
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
        $context = Context::createDefaultContext();

        $this->getContainer()->get(Connection::class)
            ->executeStatement('DROP TABLE IF EXISTS my_entity');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'TestPlugin'));

        $plugin = $this->getContainer()->get('plugin.repository')
            ->search($criteria, $context)->first();

        $this->getContainer()->get(PluginLifecycleService::class)
            ->updatePlugin($plugin, $context);
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


