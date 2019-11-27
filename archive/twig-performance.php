<?php

namespace Script;

use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Cache\FilesystemCache;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

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
        $faker = Factory::create();

        $f = $faker->randomHtml(100, 200);
        dd($f);

        $templates = [];
        $includes = [];

        for ($i = 0; $i < 200; $i++) {
            $template = '<button type="button" class="btn btn-primary">Primary</button><nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item active" aria-current="page">Home</li></ol></nav>';
            \file_put_contents(__DIR__ . '/twig-performance/includes/include_' . $i . '.html.twig', $template);

            for ($x = 0; $x < 19; $x++) {
                $templates[] = $template;
                $includes[] = "{% include 'includes/include_" . $i . ".html.twig' %}";
            }
        }

        \file_put_contents(__DIR__ . '/twig-performance/one.html.twig', \implode("\n", $templates));

        \file_put_contents(__DIR__ . '/twig-performance/include.html.twig', \implode("\n\n", $includes));

        error_log(print_r("", true) . "\n", 3, '/var/www/html/sw6/var/log/test.log');
        error_log(print_r("", true) . "\n", 3, '/var/www/html/sw6/var/log/test.log');
        error_log(print_r("", true) . "\n", 3, '/var/www/html/sw6/var/log/test.log');

        $env = new Environment(
            new FilesystemLoader([__DIR__ . '/twig-performance']),
            [
                'debug' => true,
                'cache' =>  new FilesystemCache(__DIR__ . '/twig-performance/cache/')
            ]
        );

        $time = microtime(true);

        $x = $env->render('one.html.twig');

        error_log(sprintf('%s: time[%s]', str_pad("one template", 20), round(microtime(true)-$time, 5)) . "\n", 3, '/var/www/html/sw6/var/log/test.log');

        $time = microtime(true);
        $x = $env->render('include.html.twig');
        error_log(sprintf('%s: time[%s]', str_pad("include", 20), round(microtime(true)-$time, 5)) . "\n", 3, '/var/www/html/sw6/var/log/test.log');

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

