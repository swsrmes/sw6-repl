<?php

namespace Script;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\EntityDefinitionQueryHelper;
use Shopware\Core\Framework\DataAbstractionLayer\Field\AssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Field;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Main
{
    /**
     * @var ContainerInterface
     */
    private $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function run()
    {
        $accessors = [
            'product.options.group.name',
            'product.manufacturer.id',
            'product.customFields.test',
            'product.name',
            'product.prices.currency.name'
        ];

        $definition = $this->c->get(ProductDefinition::class);

        $associations = [];

        foreach ($accessors as $accessor) {
            $fields = EntityDefinitionQueryHelper::getFieldsOfAccessor($definition, $accessor);

            $fields = array_filter($fields);

            $path = array_map(function(Field $field) {
                if ($field instanceof AssociationField) {
                    return $field->getPropertyName();
                }
                return null;
            }, $fields);

            $path = array_filter($path);

            if (empty($path)) {
                continue;
            }

            $associations[] = implode('.', $path);
        }

        dump($associations);
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();


