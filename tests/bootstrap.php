<?php

/**
 * PHPUnit Configuration Bootstrap
 * 
 * Loads application and test dependencies
 */

// Define constants
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// Override with test environment
putenv('APP_ENV=testing');
putenv('APP_DEBUG=true');
putenv('DB_DATABASE=desnkygroup_test');

// Load configuration
\App\Config::load(BASE_PATH . '/config');

// Setup test database if needed
// Uncomment to auto-create test database
// $connection = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
// $connection->execute("CREATE DATABASE IF NOT EXISTS `desnkygroup_test`");
