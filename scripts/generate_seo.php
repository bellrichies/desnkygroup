<?php

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

\App\Config::load(BASE_PATH . '/config');

(new \App\Services\SitemapService())->writePublicFiles(BASE_PATH . '/public');

echo "Generated public/sitemap.xml, public/sitemap.xml.gz, and public/robots.txt\n";
