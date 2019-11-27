<?php

namespace Script;

use Shopware\Core\Framework\Adapter\Twig\StringTemplateRenderer;
use Shopware\Core\Framework\Context;
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
        $renderer = $this->container->get(StringTemplateRenderer::class);

        $twig = \file_get_contents(__DIR__ . '/twig-rendering.twig');

        $x = $renderer->render($twig, [], Context::createDefaultContext());

        dump($x);
    }

}

$env = 'dev';
$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


