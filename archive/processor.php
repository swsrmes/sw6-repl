
<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\Error\IncompleteLineItemError;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Content\Product\Cart\ProductCartProcessor;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PluginCartProcessor implements CartProcessorInterface
{
    /**
     * @var CreditCartProcessor
     */
    private $creditCartProcessor;

    /**
     * @var ProductCartProcessor
     */
    private $productCartProcessor;

    public function __construct(CreditCartProcessor $creditCartProcessor, ProductCartProcessor $productCartProcessor)
    {
        $this->creditCartProcessor = $creditCartProcessor;
        $this->productCartProcessor = $productCartProcessor;
    }

    public function process(
        CartDataCollection $data,
        Cart $original,
        Cart $toCalculate,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {
        $lineItems = $original->getLineItems()->filterType('plugin-line-item-type');

        /*
         * Structure of the plugin line item:
         * - plugin line item
         *      - product line item(s)
         *      - credit line item(s)
         */
        foreach ($lineItems as $lineItem) {
            $this->calculate($lineItem, $original, $toCalculate, $context, $behavior, $data);
        }
    }

    private function calculate(LineItem $lineItem, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior, CartDataCollection $data): void
    {
        // check for invalid line items and remove them
        if (!$lineItem->hasChildren()) {
            $original->remove($lineItem->getId());
            $original->addErrors(new IncompleteLineItemError($lineItem->getId(), 'children'));

            return;
        }

        // create temporary cart objects to scope the following calculations
        $tempOriginal = new Cart('temp', $original->getToken());
        $tempCalculated = new Cart('temp', $original->getToken());

        // only provide the nested products and credit items
        $tempOriginal->setLineItems(
            $lineItem->getChildren()
        );

        // first start product calculation - all required data for the product processor is already loaded and stored in the CartDataCollection
        $this->productCartProcessor->process($data, $tempOriginal, $tempCalculated, $context, $behavior);

        // now calculate the credit, the credit is scoped to the already calculated products - all required data for the credit processor is already loaded and stored in the CartDataCollection
        $this->creditCartProcessor->process($data, $tempOriginal, $tempCalculated, $context, $behavior);

        // after all line items calculated - use them as new children
        $lineItem->setChildren(
            $toCalculate->getLineItems()
        );

        $toCalculate->add($lineItem);
    }
}
