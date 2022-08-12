#!/usr/bin/env bash

script_path="$(readlink -f "${BASH_SOURCE[0]}")"
script_dir="$(dirname $script_path)"
. "$script_dir/funcs.sh"

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
  background "$$" php "$script_dir/${SCRIPT}.php" "$(getPHPAutoloaderPath)" 2>&1 &
done

set +e

echo Working ... wait $USER

wait

echo Done - Have fun

