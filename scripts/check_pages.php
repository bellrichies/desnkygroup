<?php
require_once __DIR__ . '/../vendor/autoload.php';
\App\Config::load(__DIR__ . '/../config');
$c = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));
$slugs = ['home','about','services','projects','hse-policy','contact'];
foreach ($slugs as $slug) {
    $row = $c->queryOne('SELECT id, slug, is_published FROM pages WHERE slug = ?', [$slug]);
    if ($row) {
        echo sprintf("%s: id=%s published=%s\n", $row['slug'], $row['id'], $row['is_published'] ? '1' : '0');
    } else {
        echo sprintf("%s: MISSING\n", $slug);
    }
}
exit(0);
