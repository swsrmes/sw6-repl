<?php

namespace Script;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Faker\Factory;
use ONGR\ElasticsearchDSL\Query\Specialized\ScriptQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Shopware\Core\Defaults;
use Shopware\Elasticsearch\Framework\DataAbstractionLayer\ScriptIdQuery;
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
            'index' => $index,
            'body' => [
                'settings' => [
                    'index' => [
                        'mapping.total_fields.limit' => 50000
                    ]
                ]
            ]
        ]);

        $keys = $this->generateKeys();

        $mapping = [
            'properties' => [
                'id' => ['type' => 'long'],
                'default_price' => ['type' => 'double'],
            ],
            'dynamic_templates' => [
                [
                    'cheapest_price' => [
                        "match_pattern" => "regex",
                        "match" => "^rule",
                        "mapping" => [
                            "type" => "double"
                        ]
                    ]
                ]
            ]
        ];
        foreach ($keys as $key) {
            $mapping['properties'][$key] = ['type' => 'double'];
        }

        $type = 'test';
        $client->indices()->putMapping([
            'index' => $index,
            'type' => $type,
            'body' => $mapping,
            'include_type_name' => true,
        ]);

        $this->generate($keys, $index, $type);

        $client->indices()->refresh([
            'index' => $index
        ]);

        $search = new Search();

        $faker = Factory::create();

        $rules = $faker->randomElements($keys, 50);

        $rules = array_map(function(string $key) {
            return ['key' => $key, 'factor' => 2];
        }, $rules);

        $params = [
            'accessors' => $rules
        ];

        $script = "
            double getPrice(def accessors, def doc) {
                for (accessor in accessors) {
                    def key = accessor['key'];
                    if (!doc.containsKey(key) || doc[key].empty) {
                        continue;
                    }

                    def factor = accessor['factor'];
                    def value = doc[key].value * factor;
                    return (double) value;
                }

                return 0;
            }

            def accessors = params['accessors'];

            return (double)getPrice(accessors, doc);

        ";

        $sorting = new FieldSort('_script', 'asc', [
            'type' => 'number',
            'script' => [
                'lang' => 'painless',
                'params' => $params,
                'source' => $script
            ]
        ]);

        $filter = "
            double getPrice(def accessors, def doc) {
                for (accessor in accessors) {
                    def key = accessor['key'];
                    if (!doc.containsKey(key) || doc[key].empty) {
                        continue;
                    }

                    def factor = accessor['factor'];
                    def value = doc[key].value * factor;
                    return (double) value;
                }

                return 0;
            }

            def accessors = params['accessors'];

            def price = (double)getPrice(accessors, doc);

            def match = true;
            if (params.containsKey('gte')) {
                match = match && price >= params['gte'];
            }
            if (params.containsKey('gt')) {
                match = match && price > params['gt'];
            }
            if (params.containsKey('lte')) {
                match = match && price <= params['lte'];
            }
            if (params.containsKey('lt')) {
                match = match && price > params['lt'];
            }

            return match;
        ";

        $filterParams = array_merge($params, [
            'gte' => 105
        ]);

        $x = new ScriptQuery($filter, [
            'params' => $filterParams
        ]);

        $search->addQuery($x);

        $search->setScriptFields([
            'cheapest_price' => [
                'script' => [
                    'params' => $params,
                    'source' => $script
                ]
            ],
            'cheapest_price_match' => [
                'script' => [
                    'params' => $filterParams,
                    'source' => $filter
                ]
            ]
        ]);

//        $search->addSort($sorting);

        try {
            dump("search with explicit fields");
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
            $x = ['id' => $hit['_id']];

            foreach ($hit['fields'] as $field => $values) {
                $x[$field] = $values[0];
            }
            $mapping[] = $x;
        }
        dd($mapping);
    }

    private function generateKeys(): array
    {
        $rules = [];
        for ($i = 0; $i < 30; $i++) {
            $rules[] = 'rule' . $i;
        }

        $keys = [];
        for ($i = 0; $i < 5; $i++) {
            $currency = 'currency' . $i;
            foreach ($rules as $rule) {
                $keys[] = $rule . '_' . $currency;
            }
        }

        return $keys;
    }

    private function generate(array $keys, string $index, string $type)
    {

//        $mapped = [];
//        foreach ($data['documents'] as $document) {
//            $mapped[] = ['index' => ['_id' => $document['id']]];
//            $mapped[] = $document;
//        }
//
//        dump("bulk " . count($data['documents']));
//
//        $client->bulk([
//            'index' => $index,
//            'type' => $type,
//            'body' => $mapped,
//        ]);
//
//
        $faker = Factory::create();

        $client = $this->getContainer()->get(Client::class);

        $count = 500000;
        $documents = [];
        for ($i = 0; $i < $count; $i++) {
            $rules = $faker->randomElements($keys, 15);

            $document = ['id' => $i];
            foreach ($rules as $rule) {
                $document[$rule] = $faker->randomFloat(2, 50, 70);
            }

            $documents[] = ['index' => ['_id' => $i]];
            $documents[] = $document;

            if ($i % 1000 === 0) {
                dump("process: $i/$count");

                $client->bulk([
                    'index' => $index,
                    'type' => $type,
                    'body' => $documents,
                ]);

                $documents = [];
            }
        }

        if (!empty($documents)) {
            $client->bulk([
                'index' => $index,
                'type' => $type,
                'body' => $documents,
            ]);
        }
    }

}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

