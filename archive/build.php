<?php

namespace Script;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

$classLoader = require __DIR__. '/../../vendor/autoload.php';

(new Dotenv())->load(__DIR__.'/../../.env');

class Main
{
    public function run($classLoader)
    {
        $kernel = new \Shopware\Development\HttpKernel('prod', true, $classLoader);

        $response = $kernel->handle(Request::create('http://shopware.master'));
    }
}

exec('rm -rf ' . __DIR__ . '/../../var/cache');

$x = new Main();
$x->run($classLoader);



