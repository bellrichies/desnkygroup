<?php
require_once __DIR__ . '/../vendor/autoload.php';
\App\Config::load(__DIR__ . '/../config');
$c = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
$service = new \App\Services\BlogService(
    new \App\Repositories\BlogPostRepository($c),
    new \App\Repositories\BlogTaxonomyRepository($c),
    new \App\Repositories\BlogAdRepository($c),
    new \App\Services\BlogContentSanitizer(),
    null
);
$ctx = $service->listingContext([]);
echo 'posts count in context: ' . count($ctx['posts']) . PHP_EOL;
exit(0);
