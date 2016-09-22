<?php

use Plp\Task\App;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/autoloader.php';
require_once __DIR__ . '/vendor/autoload.php';


$config = [
    'database_type' => 'mysql',
    'database_name' => 'racio',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
];

if (isset($argv[1])) {
    $method = $argv[1];
}
else {
    $method = 'run';
}

$task = new App($config);
$task->$method();