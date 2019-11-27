<?php

use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Price\AmountCalculator;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Framework\DataAbstractionLayer\DefinitionInstanceRegistry;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Shopware\Storefront\Page\Product\ProductPage;

class App
{
    final private function __construct(){}

    public function detailPage(ProductPage $page, DatabaseBag $database): void {}

    public function listingPage(NavigationPage $page, DatabaseBag $database): void {}

    public function processCart(Cart $original, Cart $calculated, CartBag $bag): void {}
}

class DatabaseBag
{
    private Connection $connection;

    private DefinitionInstanceRegistry $registry;
}

class CartBag
{
    private AmountCalculator $amount;

    private PercentagePriceCalculator $percentage;

    private QuantityPriceCalculator $quantity;
}
