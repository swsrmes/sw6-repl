<?php

namespace Script;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
        $repo = $this->c->get('product.repository');

        $repo->search(new Criteria(), Context::createDefaultContext());
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();
