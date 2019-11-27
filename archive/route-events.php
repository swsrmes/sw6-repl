<?php

namespace Script;

use Shopware\Storefront\Event\StorefrontRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ExampleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'frontend.home.page.request' => 'onRequest',
            'frontend.home.page.response' => 'onResponse',
            'frontend.home.page.render' => 'onRender'
        ];
    }

    public function onRender(StorefrontRenderEvent $event): void {}

    public function onRequest(RequestEvent $event): void {}

    public function onResponse(ResponseEvent $event): void {}
}


