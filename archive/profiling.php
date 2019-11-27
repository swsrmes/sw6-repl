<?php

class A {
    public static $called = false;

    public static function trace(string $name, Closure $closure)
    {
        self::$called = true;
        return $closure();
    }
}

class B {
    public static $called = false;

    public static function trace(string $name, Closure $closure)
    {
        self::$called = true;
        return $closure();
    }
}


class Registry
{
    /**
     * Profilers will be activated over the shopware.yaml file
     *
     * All enabled profilers will be added here
     */
    private static array $profilers = [
//        A::class,
//        B::class,
    ];

    public static function trace(string $name, \Closure $closure)
    {
        $pointer = function() use ($closure) {
            return $closure();
        };

        foreach (self::$profilers as $profiler) {
            $pointer = function() use ($profiler, $name, $pointer) {
                return $profiler::trace($name, $pointer);
            };
        }

        return $pointer();
    }
}

$result = Registry::trace('test', function() {
    return 'test';
});

echo "A called " . A::$called . PHP_EOL;
echo "B called " . B::$called . PHP_EOL;
echo $result;


