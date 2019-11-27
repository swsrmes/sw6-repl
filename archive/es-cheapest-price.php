<?php

namespace Script;

use Elasticsearch\Client;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Test\IdsCollection;
use Shopware\Elasticsearch\Framework\ElasticsearchHelper;
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
        $index = $this->c->get(ElasticsearchHelper::class)
            ->getIndexName($this->c->get(ProductDefinition::class), Defaults::LANGUAGE_SYSTEM);

        $params = [
            'rules' => [null],
            'currency' => Defaults::CURRENCY,
            'default' => Defaults::CURRENCY,
            'tax' => 'gross',
            'factor' => 1
        ];

        $script = "
            def all = params['_source']['cheapestPrice'];
            def cheapest = -1;
            def tax = params['tax'];

            for (prices in all) {
                def found = 0;
                for (rule in params['rules']) {
                    if (found == 1) { break; }

                    for (price in prices) {
                        if (price['rule_id'] != rule) { continue; }

                        found = 1;

                        for (currency in price['price']) {
                            if (currency['currencyId'] == params['currencyId']) {
                                def value = currency[tax];
                                if (value < cheapest || cheapest == -1) { cheapest = value; }
                                break;
                            }
                        }

                        for (currency in price['price']) {
                            if (currency['currencyId'] == params['default']) {
                                def value = currency[tax] * params['factor'];
                                if (value < cheapest || cheapest == -1) { cheapest = value; }
                                break;
                            }
                        }

                        break;
                    }
                }
            }

            return cheapest;
        ";

        $search = [
            'index' => $index,
            'type' => 'product',
            'body' => [
                'size' => 100,
                'sort' => [
                    '_script' => [
                        'type' => 'number',
                        'order' => 'desc',
                        'script' => [
                            'source' => $script,
                            'params' => $params
                        ]
                    ]
                ],
                'script_fields' => [
                    'cheapest_price' => [
                        'script' => [
                            'params' => $params,
                            'source' => $script
                        ]
                    ]
                ]
            ]
        ];

        $result = $this->c->get(Client::class)->search($search);

    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();
