<?php
require_once __DIR__ . '/../vendor/autoload.php';
\App\Config::load(__DIR__ . '/../config');
$c = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
$count = $c->queryOne('SELECT COUNT(*) as cnt FROM blog_posts');
echo 'blog_posts count: ' . ($count['cnt'] ?? 0) . PHP_EOL;
$authors = $c->queryOne('SELECT COUNT(*) as cnt FROM blog_authors');
echo 'blog_authors count: ' . ($authors['cnt'] ?? 0) . PHP_EOL;
$cats = $c->queryOne('SELECT COUNT(*) as cnt FROM blog_categories');
echo 'blog_categories count: ' . ($cats['cnt'] ?? 0) . PHP_EOL;
$tags = $c->queryOne('SELECT COUNT(*) as cnt FROM blog_tags');
echo 'blog_tags count: ' . ($tags['cnt'] ?? 0) . PHP_EOL;
exit(0);
