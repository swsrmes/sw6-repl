<?php

namespace Script;

use Psy\Shell;

$env = 'dev';
$kernel = require __DIR__ . '/boot/boot.php';
require __DIR__ . '/vendor/autoload.php';

class Main extends BaseScript
{
    public function run()
    {
        $sh = new Shell();
        $sh->setScopeVariables(['container'=> $this->getContainer()]);
        $sh->run();
    }
}

(new Main($kernel))->run();

