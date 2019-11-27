<?php
// PHP 5.6

$array = [1,2,6,4,2,5,7,1,3,6,7];


// PHP 7 with spaceship operator

usort($array, function($a, $b) {
    return $a <=> $b;
});

print_r($array);
