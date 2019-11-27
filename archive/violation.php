<?php

namespace Script;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\EqualTo;

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
        $x = new EqualTo('a', null, 'No price for default currency provided');

        $f = $this->getContainer()->get('validator')
            ->validate('x', [$x]);

        dd($f);

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

