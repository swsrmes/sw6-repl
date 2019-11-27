<?php

$c = require  __DIR__ . '/boot/boot.php';


$count = 10000;
$values = [];
for ($i = 0; $i < $count; $i++) {
    $values[] = random_int(1, 200);
}



$time = microtime(true);
for ($i = 0; $i < $count; $i++) {
    $money = \Money\Money::EUR($values[$i]);
}

echo str_pad($count, 20) . " " . (microtime(true)-$time);
