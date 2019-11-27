<?php

namespace Script;

$env = 'dev';
$kernel = require __DIR__ . '/boot/boot.php';

class Main extends BaseScript
{
    public function run()
    {

    }
}

(new Main($kernel))->run();

