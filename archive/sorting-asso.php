<?php

namespace Script;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
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
//        $criteria = new Criteria();
//
//        $criteria
//            ->getAssociation('properties')
//            ->addSorting(new FieldSorting('name'));
//
//        $criteria->setLimit(5);
//
//        $x = $this->getContainer()->get('product.repository')
//            ->search($criteria, Context::createDefaultContext());
//
//        dd($x);



        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('active', true))
            ->addSorting(new FieldSorting('position', FieldSorting::DESCENDING))
            ->addSorting(new FieldSorting('name', FieldSorting::ASCENDING));

        $criteria
            ->getAssociation('states')
            ->addSorting(new FieldSorting('position', FieldSorting::DESCENDING))
            ->addSorting(new FieldSorting('name', FieldSorting::ASCENDING));

        $criteria->setLimit(5);

        $x = $this->getContainer()->get('country.repository')
            ->search($criteria, Context::createDefaultContext());

        dd($x);

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

