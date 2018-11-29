<?php
require_once __DIR__."/../vendor/autoload.php";

// Load .env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// load API controller
require_once __DIR__."/api.php";

// Load routes
require_once __DIR__."/routes.php";

Flight::start();