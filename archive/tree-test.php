<?php

namespace Script;

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

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


function generateCategory($parentId, $previousId, int $max, int $current) {

    $id = \Shopware\Core\Framework\Uuid\Uuid::randomHex();

    $cat = [
        'id' => $id,
        'parent' => $parentId,
        'after' => $previousId
    ];

    if ($current >= $max) {
        return $cat;
    }

    $count = random_int(2, 5);
    $children = [];

    $prev = null;
    for ($i = 1; $i <= $count; $i++) {
        $child = generateCategory($id, $prev, $max, $current + 1);
        $prev = $child['id'];
        $children[] = $child;
    }
    $cat['children'] = $children;

    return $cat;
}



$all = [];

$parentId = \Shopware\Core\Framework\Uuid\Uuid::randomHex();

$prev = null;
for ($i = 0; $i <= 4; $i++) {
    $cat = generateCategory($parentId, $prev, random_int(2, 5), 1);

    $prev = $cat['id'];

    $all[] = $cat;
}

print_r($all);



