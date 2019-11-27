<?php

namespace Script;

use Faker\Factory;
use Shopware\Storefront\Framework\Twig\Extension\SwSanitizeTwigFilter;
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
        $sanitizer = $this->getContainer()->get(SwSanitizeTwigFilter::class);

        $faker = Factory::create();
        $count = 200;

        $htmls = [];
        for ($i = 0; $i <= $count; $i++) {
            $htmls[$i] = $faker->randomHtml();
        }

        $time = microtime(true);
        for ($i = 0; $i <= $count; $i++) {
            $sanitizer->sanitize($htmls[$i]);
        }

        echo sprintf('%s: time[%s]', str_pad("done", 20), round(microtime(true)-$time, 5));
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

