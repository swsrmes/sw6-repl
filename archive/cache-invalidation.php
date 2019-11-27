<?php


$delay = 60;

$before = new DateTime();
$after = new DateTime();
$now = new DateTime();


$before->modify(sprintf('-%s seconds', $delay));

$after->modify(sprintf('+%s seconds', $delay));


echo 'now:    ' .  $now->format('Y-m-d H:i:s.v') . PHP_EOL;
echo 'before: ' .  $before->format('Y-m-d H:i:s.v') . PHP_EOL;
echo 'after:  ' .  $after->format('Y-m-d H:i:s.v') . PHP_EOL;

var_dump($now < $after); echo PHP_EOL;
var_dump($before < $now); echo PHP_EOL;
var_dump($before > $now); echo PHP_EOL;
var_dump($after > $before); echo PHP_EOL;



