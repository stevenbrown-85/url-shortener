<?php

require 'vendor/autoload.php';

use Nette\Database\Connection;
use Nette\Database\ConnectionException;
use Shortener\App;

/**
 * Load the .env file
 */
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

try {
    $dotenv->load();
} catch(Exception $e) {
    die("Error - missing .env file");
}

/**
 * Connect to the DB.
 */
$dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']}";

try {
    $connection = new Connection($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
} catch (ConnectionException $e) {
    die($e->getMessage());
}

/**
 * Create a new app instance.
 */
$app = new App($connection);

return $app;