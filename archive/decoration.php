<?php

class NotImplementedFunctionException extends Exception
{

}

abstract class Service
{
    abstract public function getDecorated(): self;

    abstract public function load(): array;

    public function loadV2(): array
    {
        throw new NotImplementedFunctionException(static::class);
    }
}

class Core extends Service
{
    public function getDecorated(): Service
    {
        throw new \RuntimeException('getDecorated');
    }

    public function load(): array
    {
        return ['foo' => 'bar'];
    }

    public function loadV2(): array
    {
        return ['v2' => 'bar'];
    }
}

class NotCompatible extends Service
{
    private Service $decorated;

    private string $key;

    public function __construct(Service $decorated, string $key)
    {
        $this->decorated = $decorated;
        $this->key = $key;
    }

    public function getDecorated(): Service
    {
        return $this->decorated;
    }

    public function load(): array
    {
        $array = $this->decorated->load();

        $array[$this->key] = true;

        return $array;
    }
}

class Plugin extends Service
{
    private Service $decorated;

    private string $key;

    public function __construct(Service $decorated, string $key)
    {
        $this->decorated = $decorated;
        $this->key = $key;
    }

    public function getDecorated(): Service
    {
        return $this->decorated;
    }

    public function load(): array
    {
        $array = $this->decorated->load();
        $array[$this->key] = true;

        return $array;
    }

    public function loadV2(): array
    {
        $array = $this->decorated->loadV2();
        $array['v2.' . $this->key] = true;

        return $array;
    }
}


$plugin = new Plugin(new Plugin(new Core(), 'p1'),'p2');

try {
    $result = $plugin->loadV2();
} catch (NotImplementedFunctionException $e) {
    $result = $plugin->load();
}

print_r($result);




$plugin = new Plugin(new NotCompatible(new Core(), 'p1'),'p2');

try {
    $result = $plugin->loadV2();
} catch (NotImplementedFunctionException $e) {
    $result = $plugin->load();
}

print_r($result);




if (Feature::isActive('major.load-v2')) {
    return $plugin->loadV2();
}

return $plugin->load();




class Feature
{
    public static function isActive(string $key): bool {
        return true;
    }
}

