<?php
require_once __DIR__ . '/../vendor/autoload.php';
\App\Config::load(__DIR__ . '/../config');
$c = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
$rows = $c->query('SELECT id, title, slug, status, published_at FROM blog_posts ORDER BY created_at DESC LIMIT 50');
foreach ($rows as $r) {
    echo $r['id'] . ' ' . $r['slug'] . ' ' . $r['status'] . ' ' . ($r['published_at'] ?? 'null') . PHP_EOL;
}
exit(0);
