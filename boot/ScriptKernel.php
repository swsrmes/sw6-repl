<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ScriptKernel extends \Shopware\Core\Kernel
{
    protected function build(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition->isAbstract()) {
                continue;
            }
            $definition->setPublic(true);
        }
        return $container;
    }

//    public function registerBundles()
//    {
//        return require __DIR__ .'/../../../config/bundles.php';
//    }
    public function getProjectDir()
    {
        return __DIR__ . '/../../..';
    }
}
