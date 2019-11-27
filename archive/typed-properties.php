<?php

require  __DIR__ . '/boot/boot.php';

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\System\Tax\TaxEntity;

if (\Shopware\Core\Framework\Feature::isActive('TEST123')) { // is this possible???  :D
    class ProductEntity
    {
        public string $id;
        public ?string $name = null;                            // optional field
        public TaxEntity $tax;                                  // autoload true in definition, and required
        public ?ProductManufacturerEntity $manufacturer = null; // auto load false, and/or not required
        public ?CategoryCollection $categories = null;          // many to many association (always autoload false)

        public function getCategories(): CategoryCollection
        {
            return $this->categories ?? new CategoryCollection();
        }
    }

    return;
}


class ProductEntity
{
    public string $id;
    public ?string $name = null;                            // optional field
    public TaxEntity $tax;                                  // autoload true in definition, and required
    public ?ProductManufacturerEntity $manufacturer = null; // auto load false, and/or not required
    public ?CategoryCollection $categories = null;          // many to many association (always autoload false)

    public function getCategories(): CategoryCollection
    {
        return $this->categories ?? new CategoryCollection();
    }
}


