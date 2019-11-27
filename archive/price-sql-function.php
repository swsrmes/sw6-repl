<?php

namespace Script;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\MultiInsertQueryQueue;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
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
        $criteria = new Criteria();
        $criteria->addField('listingPrices');
        $criteria->addField('prices.id');
        $criteria->setLimit(4000);

        /** @var EntityRepositoryInterface $repo */
        $repo = $this->c->get('product.repository');

        $time = microtime(true);
        $result = $repo->searchIds($criteria, Context::createDefaultContext());
        echo str_pad("load 5000", 20) . " " . (microtime(true)-$time);

        $priceIds = array_column($result->getData(), 'prices.id');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('prices.id', array_filter($priceIds)));
        $criteria->addSorting(new FieldSorting('prices.price'));
        $criteria->setLimit(50);

        $time = microtime(true);
        $x = $repo->search($criteria, Context::createDefaultContext());

        echo PHP_EOL;
        dump($x->getTotal());

        echo str_pad("load 5000", 20) . " " . (microtime(true)-$time);
        dd();

        dump($result);


        die();

 //    $this->updateDatabase();

        $count = 100000;

//        $this->insertData($count);

        $sql = <<<SQL
SELECT SQL_NO_CACHE id, prices
FROM cheapest_price_test
LIMIT 10;
SQL;

        $time = microtime(true);
        $x = $this->c->get(Connection::class)->fetchAll($sql);

        dd($x);

        dump(str_pad("fetching $count records ", 20) . " " . (microtime(true)-$time));

        return;

    }

    private function insertData(int $count): int
    {
        $queue = new MultiInsertQueryQueue($this->c->get(Connection::class));

        $this->c->get(Connection::class)
            ->executeUpdate('DELETE FROM cheapest_price_test');

        for ($i = 0; $i < $count; $i++) {

            $prices = [
                [
                    'rule1_euro_gross' => 100,
                    'rule2_euro_gross' => 300,
                    'rule2_dollar_gross' => 200,
                    'default_euro_gross' => 150,
                ],
                [
                    'rule3_euro_gross' => 150,
                    'rule3_dollar_gross' => 200,
                    'default_euro_gross' => 150,
                ],
                [
                    'rule4_euro_gross' => 50,
                    'default_euro_gross' => 30
                ]

                /*
                'gross' => [
                    [
                        'rule1' => ['euro' => 100],
                        'rule2' => ['euro' => 300, 'dollar' => 200],
                        'default' => ['euro' => 150]
                    ],
                    [
                        'rule3' => ['dollar' => 200],
                        'default' => ['euro' => 150]
                    ],
                    [
                        'rule4' => ['euro' => 50],
                        'default' => ['euro' => 30],
                    ]
                ]*/
            ];

            $queue->addInsert('cheapest_price_test', [
                'parent_id' => $i,
                'product_id' => $i,
                'prices' => json_encode($prices)
            ]);
        }

        $queue->execute();

        return $count;
    }

    private function updateDatabase()
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS cheapest_price_test;

CREATE TABLE `cheapest_price_test` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `product_id` int NOT NULL,
  `prices` json NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SQL;

        $this->c->get(Connection::class)
            ->executeUpdate($sql);

        $sql = <<<SQL
DROP FUNCTION IF EXISTS cheapest_price;

DELIMITER $$

CREATE FUNCTION cheapest_price (s JSON, rules JSON, currencies JSON)
RETURNS FLOAT
DETERMINISTIC

    BEGIN
        RETURN JSON_EXTRACT(s, '$.gross.variant');

        SET @prices = JSON_EXTRACT(s, '$.gross[0].default.euro');

        SET @variant_count = JSON_LENGTH(@prices);

        SET @rule_count = JSON_LENGTH(rules);

        SET @currency_count = JSON_LENGTH(currencies);

        SET @matches = JSON_ARRAY();

        SET @variant_index = 0;

        WHILE @variant_index < @variant_count DO

            SET @rule_index = 0;

            SET @variant = JSON_EXTRACT(@prices, CONCAT('$[', @variant_index ,']'));

            WHILE @rule_index < @rule_count DO

                SET @rule = JSON_EXTRACT(rules, CONCAT('$[', @rule_index, ']'));

                SET @accessor = CONCAT('$.', @rule);

                IF JSON_CONTAINS_PATH(@variant, 'one', @accessor) THEN

                    SET @price = JSON_EXTRACT(@variant, @accessor);

                    SET @currency_index = 0;

                    WHILE @currency_index < @currency_count DO

                        SET @currency = JSON_EXTRACT(currencies, CONCAT('$[', @currency_index ,']'));

                        IF JSON_CONTAINS_PATH(@price, 'one', CONCAT('$.', @currency)) THEN

                            SET @currency_price = JSON_EXTRACT(@price, CONCAT('$.', @currency));

                            SET @matches = JSON_ARRAY_APPEND(@matches, '$', @currency_price);

                            SET @currency_index = @currency_count;

                        END IF;

                        SET @currency_index = @currency_index + 1;

                    END WHILE;

                    SET @rule_index = @rule_count;

                END IF;

                SET @rule_index = @rule_index + 1;

            END WHILE;

            SET @variant_index = @variant_index + 1;

        END WHILE;

        SET @match_count = JSON_LENGTH(@matches);

        SET @match_index = 0;

        SET @cheapest_price = -1;

        WHILE @match_index < @match_count DO

            SET @match = JSON_UNQUOTE(JSON_EXTRACT(@matches, CONCAT('$[', @match_index ,']')));

            SET @match = CAST(@match as FLOAT);

            IF @cheapest_price = -1 THEN
                SET @cheapest_price = @match;
            END IF;

            IF @match < @cheapest_price THEN
                SET @cheapest_price = @match;
            END IF;

            SET @match_index = @match_index + 1;
        END WHILE;

        RETURN @cheapest_price;

    END$$

DELIMITER ;

SQL;

        $this->c->get(Connection::class)
            ->executeUpdate($sql);

    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();
