<?php

$api = new Api();

Flight::route('/', function(){
  echo "Welcome to Bus Schedule REST API!";
});

Flight::route('/bus', [$api, 'get_bus']);