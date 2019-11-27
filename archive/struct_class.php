<?php

namespace Script;


class Main
{

    public function run()
    {
$time = microtime(true);
        for ($i = 0; $i < 5000; $i++) {
            $x = new TestEntity();
        }
        echo sprintf('%s: time[%s]', str_pad("direct new instance", 20), round(microtime(true)-$time, 5)) . "\n";


        $time = microtime(true);
        for ($i = 0; $i < 5000; $i++) {
            $x = self::create(TestEntity::class);
        }
        echo sprintf('%s: time[%s]', str_pad("factory class", 20), round(microtime(true)-$time, 5)) . "\n";

    }

    protected static $instances = [];

    public static function create(string $class)
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return clone self::$instances[$class];
    }

}

$x = new Main();
$x->run();


class TestEntity
{
    protected $foo;
    protected $bar;
}
