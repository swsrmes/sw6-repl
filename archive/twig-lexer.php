<?php

namespace Script;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;
use Twig\Lexer;
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
        $script = <<<TWIG
        {% set test = 1 %}

        <h1>{{ test }}</h1>
TWIG;

        $twig = new Environment(new ArrayLoader());
        $name = md5($script);
        $twig->setLoader(new ArrayLoader([$name => $script]));

//        $twig->setLexer(
//            new Lexer($twig, [
//                'tag_block' => ['', ''],
//                'tag_variable' => ['', '']
//            ])
//        );


        $x = $twig->render($name, []);
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


