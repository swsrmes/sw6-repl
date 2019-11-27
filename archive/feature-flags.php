<?php

$container = require  __DIR__ . '/boot/boot.php';

class Service
{
//    public function foo()

    public function foo(?string $required = null)
    {
        echo "foo";
    }
}

class NewService
{
    public function bar()
    {
        echo "bar";
    }
}

/**
 * @internal (flag:FEATURE_NEXT_10000)  => remove by release completly
 */
class NewFeature
{
    public function bar()
    {
        echo "bar";
    }
}

class Controller
{
    public function action()
    {
        if (Feature::isActive('v6.5.0')) {
            $service = new Service();
            $service->foo();
        } else {
            $service = new NewService();
            $service->bar();
        }
    }
}




/*
 * 1) feature flags for feature development
 *  - when to use a feature flag?
 *  - is it usable for customers?
 *  - who decide the feature set of a release?
 *
 * 2) feature flags for major breaks
 *
 *
 * DOD:
 *
 * - Ein Feature = Ein Feature branch
 * -
 *
 *
 */
