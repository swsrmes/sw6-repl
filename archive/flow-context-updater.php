<?php

namespace Script;

use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\OrderAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use const Shopware\Core\Checkout\Order\Event\OrderPaymentMethodChangedEvent;

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

    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


class OrderContextUpdater implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'order.state.changed.open' => 'updateContext',
            OrderPaymentMethodChangedEvent::EVENT_NAME => 'updateContext'
        ];
    }

    /**
     * @internal
     * @private
     */
    public function updateContext(OrderAware $event): void
    {
        $this->update($event->getOrderId(), $event->getContext());
    }

    /**
     * @public
     */
    public function update(string $orderId, Context $context): void
    {
        // >>>> will execute:
        $order = $this->getOrder($orderId);

        $cart = $this->createCartByOrder($order);

        $ruleIds = $this->getRuleIds($cart, $context);

        $context->setRuleIds($ruleIds);
    }
}


