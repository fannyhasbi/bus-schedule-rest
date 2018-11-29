<?php
require_once __DIR__."/../vendor/autoload.php";

// Load .env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

Flight::route('/', function(){
  echo "Welcome to Bus Schedule REST API!";
});

Flight::start();