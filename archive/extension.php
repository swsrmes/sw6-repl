<?php

class Base
{
    public function main()
    {
        $this->test();
    }

    private function test() {
        echo "origin" . PHP_EOL;
    }
}


class Extension extends Base
{
    /**
     * @var ReflectionClass
     */
    private $class;

    public function main()
    {
        $this->class = new ReflectionClass(self::class);

        $this->class
            ->getMethod('test')
            ->setAccessible(true);

    }

    private function test() {

        parent::test();

        echo "test2";
    }
}

$x = new Extension();
$x->main();
