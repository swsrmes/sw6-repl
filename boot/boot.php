<?php

use Shopware\Core\HttpKernel;
use Symfony\Component\Dotenv\Dotenv;
use Composer\Autoload\ClassLoader;

$classLoaderPath = $argv[1];
$executionDir = $argv[2];

if (empty($classLoaderPath)) {
    throw new \Exception("vendor/autoload.php not found");
}
$classLoader = require $classLoaderPath;

require __DIR__ . '/ScriptKernel.php';
require __DIR__ . '/api.php';
require __DIR__ . '/store-api.php';
require __DIR__ . '/base-script.php';

$projectRoot = $executionDir ?? dirname(__DIR__) . '/../..';

if (class_exists(Dotenv::class) && (file_exists($projectRoot . '/.env.local.php') || file_exists($projectRoot . '/.env') || file_exists($projectRoot . '/.env.dist'))) {
    (new Dotenv())->usePutenv()->bootEnv($projectRoot . '/.env');
}

$returnKernel = $returnKernel ?? false;

$env = $env ?? 'prod';

$kernel = new class($projectRoot, $env, $env !== 'prod', $classLoader) extends HttpKernel {
    protected static string $kernelClass = ScriptKernel::class;

    public function __construct(string $projectRoot, string $environment, bool $debug, ?ClassLoader $classLoader = null)
    {
        parent::__construct($environment, $debug, $classLoader);
        $this->projectDir = $projectRoot;
    }
};

$kernel->getKernel()->boot();

return $kernel;
