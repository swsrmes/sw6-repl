<?php

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Flow\Dispatching\Action\FlowAction;
use Shopware\Core\Framework\Event\CustomerAware;
use Shopware\Core\Framework\Event\FlowEvent;

class CheckoutOrderPlacedEvent implements CustomerAware
{
    protected OrderEntity $order;

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    public function getCustomerId(): string
    {
        $customer = $this->getOrder()->getOrderCustomer();

        if ($customer === null || $customer->getCustomerId() === null) {
            throw new CustomerDeletedException();
        }

        return $customer->getCustomerId();
    }
}

class AddCustomerTagAction extends FlowAction
{
    public function handle(FlowEvent $event): void
    {
        $baseEvent = $event->getEvent();

        try {
            $customerId = $baseEvent->getCustomerId();
        } catch(CustomerDeletedException $e) {
            return;
        }

        // ....
    }
}
