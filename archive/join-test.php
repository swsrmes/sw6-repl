<?php

namespace Script;

use Shopware\Core\Content\Test\Product\ProductBuilder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\AndFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NandFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\Test\IdsCollection;
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
        $ids = new IdsCollection();

//        $criteria->addFilter(
//            new AndFilter([
//                new EqualsFilter('categories.id', $ids->get('c1')),
//                new EqualsFilter('categories.active', true),
//                new EqualsFilter('categories.id', $ids->get('c2')),
//            ])
//        );

        $criteria = new Criteria();


        $criteria->addFilter(

            // alle produkte die kategorie c1 UND c2 zugeordnet sind
            new AndFilter([
                new EqualsFilter('categories.id', $ids->get('c1')),
            ]),
            new AndFilter([
                new EqualsFilter('categories.id', $ids->get('c2')),
            ]),
        );


        $criteria->addFilter(
            // alle produkte die EINER aktiven Kategorie UND Kategorie c2 zugeordnet sind wenn diese aktiv ist
            new AndFilter([
                new EqualsFilter('categories.active', true)
            ]),
            new AndFilter([
                new EqualsFilter('categories.id', $ids->get('c2')),
                new EqualsFilter('categories.active', false)
            ]),
        );


        $criteria->addFilter(
            // alle produkte die DER aktiven c1 Kategorie UND der Kategorie c2 zugeordnet sind
            new AndFilter([
                new EqualsFilter('categories.id', $ids->get('c2')),
                new EqualsFilter('categories.active', true)
            ]),
            new AndFilter([
                new EqualsFilter('categories.id', $ids->get('c1')),
                new EqualsFilter('categories.active', false)
            ]),
        );

        $criteria->addFilter(
            // alle produkte die (aktiv sind UND (c1 zugeordnet sind UND c2 zugeordnet sind) ODER (c3 zugeordnet und inaktiv sind))

            new OrFilter([
                new AndFilter([
                    new EqualsFilter('active', true),
                    new AndFilter([
                        new EqualsFilter('categories.id', $ids->get('c1')),
                    ]),
                    new AndFilter([
                        new EqualsFilter('categories.id', $ids->get('c2')),
                    ]),
                ]),
                new AndFilter([
                    new EqualsFilter('categories.id', $ids->get('c3')),
                    new EqualsFilter('active', false),
                ]),
            ])
        );



        $criteria->addFilter(

            // alle produkte die kategorie c1 UND NICHT c2 zugeordnet sind
            new AndFilter([
                new EqualsFilter('categories.id', $ids->get('c1')),
            ]),
            new NandFilter([
                new EqualsFilter('categories.id', $ids->get('c2')),
            ]),
        );




        $criteria->addFilter(
            new OrFilter([
                new EqualsFilter('properties.id', $ids->get('red')),
                new AndFilter([
                    new OrFilter([
                        new AndFilter([
                            new AndFilter([
                                new EqualsFilter('categories.id', $ids->get('c1')),
                                new EqualsFilter('categories.active', true),
                            ]),
                            new AndFilter([
                                new EqualsFilter('categories.id', $ids->get('c2')),
                                new EqualsFilter('categories.active', true),
                            ]),
                        ]),
                        new AndFilter([
                            new EqualsFilter('categories.id', $ids->get('c3')),
                            new EqualsFilter('categories.active', false),
                        ]),
                    ])
                ])
            ]
        ));

//        $criteria->addFilter(
//            new EqualsFilter('categories.id', $ids->get('c1')),
//        );
//        $criteria->addFilter(
//            new EqualsFilter('categories.id', $ids->get('c2')),
//        );

        $x = $this->getContainer()->get('product.repository')->searchIds($criteria, Context::createDefaultContext());
        dd($x->getIds());

//    }
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


