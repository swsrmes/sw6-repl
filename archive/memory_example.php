<?php


$big_array = array();

$time = microtime(true); $memory = memory_get_peak_usage();
error_log(str_pad("start tracing $label$", 20) . "\n", 3, '/var/log/test.log');

for ($i = 0; $i < 1000000; $i++)
{
    $big_array[] = $i;
}

error_log(sprintf('%s: time[%s] || used[%s] || current[%s]', str_pad("$label$", 20), (microtime(true)-$time), memory_get_usage() - $memory / 1024, memory_get_usage() / 1024). "\n", 3, '/var/log/test.log');


echo 'After building the array.' . PHP_EOL;
print_mem();

unset($big_array);

echo 'After unsetting the array.' . PHP_EOL;
print_mem();



function print_mem()
{
    /* Currently used memory */
    $mem_usage = memory_get_usage();

    /* Peak memory usage */
    $mem_peak = memory_get_peak_usage();

    echo 'The script is now using: <strong>' . round(memory_get_usage() / 1024) . 'KB</strong> of memory.' . PHP_EOL;
    echo 'Peak usage: <strong>' . round(memory_get_peak_usage() / 1024) . 'KB</strong> of memory.' . PHP_EOL . PHP_EOL;
}
