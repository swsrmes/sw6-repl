<?php

namespace Script;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MyStruct
{
    public int $var1;
    public int $var2;
    public int $var3;
    public int $var4;
    public int $var5;
    public int $var6;
    public int $var7;
    public int $var8;
    public int $var9;
    public int $var10;
    public int $var11;

    public function setVar1(int $var1): void { $this->var1 = $var1; }
    public function setVar2(int $var2): void { $this->var2 = $var2; }
    public function setVar3(int $var3): void { $this->var3 = $var3; }
    public function setVar4(int $var4): void { $this->var4 = $var4; }
    public function setVar5(int $var5): void { $this->var5 = $var5; }
    public function setVar6(int $var6): void { $this->var6 = $var6; }
    public function setVar7(int $var7): void { $this->var7 = $var7; }
    public function setVar8(int $var8): void { $this->var8 = $var8; }
    public function setVar9(int $var9): void { $this->var9 = $var9; }
    public function setVar10(int $var10): void { $this->var10 = $var10; }
    public function setVar11(int $var11): void { $this->var11 = $var11; }

    public function assign(array $data)
    {
        foreach ($data as $key => $value) {
            if ($key === 'id' && method_exists($this, 'setId')) {
                $this->setId($value);

                continue;
            }

            try {
                $this->$key = $value;
            } catch (\Error | \Exception $error) {
                // nth
            }
        }
    }

    public function assignOptimized(array $data)
    {
        if (array_key_exists('id', $data) && method_exists($this, 'setId')) {
            $this->setId($data['id']);
        }
        foreach ($data as $key => $value) {
            try {
                $this->$key = $value;
            } catch (\Error | \Exception $error) {
                // nth
            }
        }
    }

    public function assignSetter(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);

            try {
                $this->$method($value);
            } catch (\Error | \Exception $error) {
                // nth
            }
        }
    }

}

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
        $data = [
            'var1' => 1,
            'var2' => 2,
            'var3' => 3,
            'var4' => 4,
            'var5' => 5,
            'var6' => 6,
            'var7' => 7,
            'var8' => 8,
            'var9' => 9,
            'var10' => 0,
            'var11' => 1,
        ];
        $count = 10000;

        $x = new MyStruct();
$time = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $x->assign($data);
        }
        echo sprintf('%s: time[%s]', str_pad("current", 20), round(microtime(true)-$time, 5));

        echo PHP_EOL;


$time = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $x->assignOptimized($data);
        }
        echo sprintf('%s: time[%s]', str_pad("optimized", 20), round(microtime(true)-$time, 5));
        echo PHP_EOL;


        $time = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            $x->assignSetter($data);
        }
        echo sprintf('%s: time[%s]', str_pad("setter", 20), round(microtime(true)-$time, 5));
        echo PHP_EOL;



        $time = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            if (isset($data['var1'])) { $x->var1 = 1; }
            if (isset($data['var2'])) { $x->var2 = 1; }
            if (isset($data['var3'])) { $x->var3 = 1; }
            if (isset($data['var4'])) { $x->var4 = 1; }
            if (isset($data['var5'])) { $x->var5 = 1; }
            if (isset($data['var6'])) { $x->var6 = 1; }
            if (isset($data['var7'])) { $x->var7 = 1; }
            if (isset($data['var8'])) { $x->var8 = 1; }
            if (isset($data['var9'])) { $x->var9 = 1; }
            if (isset($data['var10'])) { $x->var10 = 1; }
            if (isset($data['var11'])) { $x->var11 = 1; }
        }
        echo sprintf('%s: time[%s]', str_pad("known vars", 20), round(microtime(true)-$time, 5));
        echo PHP_EOL;



        $time = microtime(true);
        for ($i = 0; $i < $count; $i++) {
            if (array_key_exists('var1', $data)) { $x->var1 = 1; }
            if (array_key_exists('var2', $data)) { $x->var2 = 1; }
            if (array_key_exists('var3', $data)) { $x->var3 = 1; }
            if (array_key_exists('var4', $data)) { $x->var4 = 1; }
            if (array_key_exists('var5', $data)) { $x->var5 = 1; }
            if (array_key_exists('var6', $data)) { $x->var6 = 1; }
            if (array_key_exists('var7', $data)) { $x->var7 = 1; }
            if (array_key_exists('var8', $data)) { $x->var8 = 1; }
            if (array_key_exists('var9', $data)) { $x->var9 = 1; }
            if (array_key_exists('var10', $data)) { $x->var10 = 1; }
            if (array_key_exists('var11', $data)) { $x->var11 = 1; }
        }
        echo sprintf('%s: time[%s]', str_pad("known vars", 20), round(microtime(true)-$time, 5));
        echo PHP_EOL;



    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

