<?php declare(strict_types=1);


$time = microtime(true);
for ($i = 1; $i < 100000; $i++) {
    $y = (float) (string) $i;
}
die((microtime(true)-$time));


$a = 0.631;



$x = $a * 5.0;

$y = (float) (string) $x;

var_dump($x);
var_dump(serialize($x));

var_dump($y);
var_dump(serialize($y));

if ($y === 3.155) {
    die('match');
}



