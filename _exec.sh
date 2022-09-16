#!/usr/bin/env bash

EXECUTION_DIR=$(dirname "$0");
SCRIPT_PATH=$(readlink -f "${BASH_SOURCE[0]}")
SCRIPT_DIR=$(dirname "$SCRIPT_PATH")
. "$SCRIPT_DIR/funcs.sh"
autoloader=$(getPHPAutoloaderPath "$EXECUTION_DIR");

handler() {
  echo 'Failed to execute php script';
  trap - INT

  exit 1
}

trap handler INT

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

set -e

for _ in $(seq 1 "${THREADS}");
do
  background "$$" php "$SCRIPT_DIR/${SCRIPT}.php" "$autoloader" "$EXECUTION_DIR" 2>&1 &
done

set +e

echo Working ... wait $USER

wait

echo Done - Have fun

