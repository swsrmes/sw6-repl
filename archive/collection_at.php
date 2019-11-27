<?php

namespace Script;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Loader\ArrayLoader;

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

        $loader = new ArrayLoader([
            'mail.html.twig' => '{{ collection }}',
        ]);

        $source = $loader->getSourceContext('mail.html.twig');

        $twig = $this->c->get('twig');

        $stream = $twig->tokenize($source);

        $parsed = $twig->parse($stream);

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

