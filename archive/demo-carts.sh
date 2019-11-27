#!/usr/bin/env bash

export APP_ENV=prod

for x in $(seq 1 16); do
  php demo-carts.php 2>&1 &
done

echo Working ... wait oliver

wait

echo Done - Have fun
