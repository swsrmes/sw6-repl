<?php

namespace Script;

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
        $criteria = new Criteria();
        $criteria->addFields([
            'id',
            'name',
            'categories.id',
            'categories.name',
            'manufacturer.name',
            'categories.media.id'
        ]);

        $fields = [];
        if (!empty($criteria->getFields())) {
            foreach ($criteria->getFields() as $field) {
                $pointer = &$fields;
                $parts = explode('.', $field);

                foreach ($parts as $part) {
                    if (!isset($pointer[$part])) {
                        $pointer[$part] = [];
                    }

                    $pointer = &$pointer[$part];
                }
            }
        }
        dd($fields);
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

