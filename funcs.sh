#!/usr/bin/env bash

background() {
  local -ir pid="$1"
  shift
  "$@" || kill -INT -- -"$pid"
}

getPHPAutoloaderPath() {
  if [[ -f 'vendor/autoload.php' ]]
  then
    realpath 'vendor/autoload.php';
  elif [[ -f '/../../../vendor/autoload.php' ]]
  then
    realpath'/../../../vendor/autoload.php';
  fi
}