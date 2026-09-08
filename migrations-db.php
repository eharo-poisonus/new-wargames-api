<?php

use Symfony\Component\Dotenv\Dotenv;

require './vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

return [
    'dbname' => $_ENV['MYSQL_DB'] ?? getEnv('MYSQL_DB'),
    'user' => $_ENV['MYSQL_USER'] ?? getEnv('MYSQL_USER'),
    'password' => $_ENV['MYSQL_PASSWORD'] ?? getEnv('MYSQL_PASSWORD'),
    'host' => $_ENV['MYSQL_HOST'] ?? getEnv('MYSQL_HOST'),
    'driver' => $_ENV['MYSQL_DRIVER'] ?? getEnv('MYSQL_DRIVER')
];
