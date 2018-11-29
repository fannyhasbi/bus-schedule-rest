<?php

$api = new Api();

Flight::route('/', function(){
  echo "Welcome to Bus Schedule REST API!";
});

Flight::route('GET /bus', [$api, 'get_bus']);
Flight::route('POST /add-bus', [$api, 'add_bus']);

Flight::route('GET /departure', [$api, 'get_departure']);

Flight::route('*', function(){
  Flight::json([
    'status'  => 404,
    'message' => 'Not Found',
    'data'    => null
  ]);
});