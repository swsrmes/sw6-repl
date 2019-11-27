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

    public function run(Context $context)
    {
        $renderer = new StringTemplateRenderer($this->getContainer()->get('twig'));

        $template = <<<'EOD'
        {% if context.currencyId === 'test' %}
            {{ 'test' }}
        {% else %}
            {{ 'not test' }}
        {% endif %} 
EOD;
        $renderer->render($template, ['c' => $context], $context);
    }

}

$env = 'dev';
$x = new Main(require  __DIR__ . '/boot/boot.php');

$count = 100;

$context = Context::createDefaultContext();
$time = microtime(true);
for ($i = 0; $i < $count; $i++) {
    $x->run($context);
}

echo sprintf('%s: time[%s]', str_pad("", 20), round(microtime(true)-$time, 5));



