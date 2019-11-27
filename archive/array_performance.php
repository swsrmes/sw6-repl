<?php

for ($i = 0; $i <= 10000; $i++) {
    $array[] = 'foo' . $i;
}

$time = microtime(true);
$x = array_filter(array_unique($array));
echo '%s: time[%s]', str_pad("filter + unique", 20), round(microtime(true)-$time, 5) . PHP_EOL;


$time = microtime(true);
$ids = array_filter(array_keys(array_flip($array)));
echo '%s: time[%s]', str_pad("flip + keys", 20), round(microtime(true)-$time, 5) . PHP_EOL;






