#!/usr/bin/env bash

export APP_ENV=prod

SCRIPT='_test'
THREADS=32

if [[ -n "$1" ]]; then
    SCRIPT=$1
fi

if [[ -n "$2" ]]; then
    THREADS=$2
fi

echo "execute ${SCRIPT} in ${THREADS} threads"

for x in $(seq 1 ${THREADS}); do
  php "${SCRIPT}.php" 2>&1 &
done

echo Working ... wait oliver

wait

echo Done - Have fun

