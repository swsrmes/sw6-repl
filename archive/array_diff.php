<?php

$state = ['de', 'en', 'fr'];

$delete = ['en'];

print_r(array_diff($state, $delete));


print_r(array_intersect($state, $delete));
