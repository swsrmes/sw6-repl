<?php

namespace Script;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Faker\Factory;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
        $client = $this->getContainer()->get(Client::class);

        $index = 'test-index';
        if ($client->indices()->exists(['index' => $index])) {
            $client->indices()->delete(['index' => $index]);
        }

        $client->indices()->create([
            'index' => $index
        ]);

        $data = $this->generate();

        $mapping = [
            'properties' => [
                'id' => ['type' => 'long'],
                'default_price' => ['type' => 'double'],
                'cheapest_price' => ['type' => 'keyword']
            ]
        ];

        $type = 'test';
        $client->indices()->putMapping([
            'index' => $index,
            'type' => $type,
            'body' => $mapping,
            'include_type_name' => true,
        ]);

        $mapped = [];
        foreach ($data['documents'] as $document) {
            $mapped[] = ['index' => ['_id' => $document['id']]];
            $mapped[] = $document;
        }

        dump("bulk " . count($data['documents']));

        $client->bulk([
            'index' => $index,
            'type' => $type,
            'body' => $mapped,
        ]);

        $client->indices()->refresh([
            'index' => $index
        ]);

        $search = new Search();

        $faker = Factory::create();

        $params = [
            'keys' => $faker->randomElements($data['keys'], 50)
        ];

        $script = "
            def keys = params['keys'];
            def prices = doc['cheapest_price'].value.splitOnToken('||');

            for (key in keys) {
                for (priceString in prices) {
                    def price = priceString.splitOnToken('::');

                    if (price[0] != key) {
                        continue;
                    }

                    return Double.parseDouble(price[1]);
                }
            }

            return doc['default_price'].value;
        ";

        $sorting = new FieldSort('_script', 'asc', [
            'type' => 'number',
            'script' => [
                'lang' => 'painless',
                'params' => $params,
                'source' => $script
            ]
        ]);

        $search->setScriptFields([
            'cheapest_price' => [
                'script' => [
                    'params' => $params,
                    'source' => $script
                ]
            ]
        ]);

        $search->addSort($sorting);

        try {
            dump("search with string explode");
            $time = microtime(true);
            $result = $client->search([
                'index' => $index,
                'type' => $type,
                'body' => $search->toArray()
            ]);
            dump(round(microtime(true)-$time, 5));

        } catch (BadRequest400Exception $e) {
            $error = json_decode($e->getMessage(), true);
            dd($e->getMessage());
        }

        $mapping = [];
        foreach ($result['hits']['hits'] as $hit) {
            $mapping[$hit['_id']] = $hit['fields']['cheapest_price'][0];
        }
        dd($mapping);
    }

    private function generate()
    {
        $rules = [];
        for ($i = 0; $i < 200; $i++) {
            $rules[] = 'rule' . $i;
        }

        $currencies = [];
        for ($i = 0; $i < 5; $i++) {
            $currencies[] = 'currency' . $i;
        }

        $faker = Factory::create();

        $keys = [];
        $documents = [];
        for ($i = 0; $i < 50000; $i++) {
            $ruleOffset = $faker->randomElements($rules, 1);

            $prices = [];
            foreach ($ruleOffset as $rule) {
                foreach ($currencies as $currency) {
                    $key = $rule . '_' . $currency . '_gross';
                    $price = $faker->randomFloat(2, 50, 1000);

                    $prices[] = $key . '::' . $price . '::1';

                    $keys[] = $key;
                }
            }

            $document = [
                'id' => $i,
                'default_price' => $faker->randomFloat(2, 50, 1000),
                'cheapest_price' => implode('||', $prices)
            ];

            $documents[] = $document;
        }

        return [
            'keys' => $keys,
            'documents' => $documents
        ];
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

