<?php

use Shopware\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

abstract class SecuredRoute
{
    abstract public function verify(Request $request, SalesChannelContext $context): void;
}

abstract class AbstractOrderRoute123 extends SecuredRoute
{}

class OrderRoute123 extends AbstractOrderRoute123
{
    public function verify(Request $request, SalesChannelContext $context): void
    {
        if (!$context->getCustomer()) {
            throw new CustomerNotLoggedInException();
        }
    }

    /**
     * @RouteScope('store-api')
     */
    public function route(Request $request, SalesChannelContext $context): void
    {
        $this->verify($request, $context);

        $this->internal($request, $context);
    }

    /**
     * @RouteScope('admin')
     */
    public function internal(Request $request, SalesChannelContext $context): void
    {
    }
}



