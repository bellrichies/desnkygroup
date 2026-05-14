<?php

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

\App\Config::load(BASE_PATH . '/config');

$checks = [
    'sitemap_exists' => is_file(BASE_PATH . '/public/sitemap.xml'),
    'robots_exists' => is_file(BASE_PATH . '/public/robots.txt'),
    'minified_css_exists' => is_file(BASE_PATH . '/public/assets/css/main.min.css'),
    'analytics_js_exists' => is_file(BASE_PATH . '/public/assets/js/analytics.js'),
    'upload_execution_blocked' => is_file(BASE_PATH . '/public/uploads/.htaccess'),
    'sitemap_has_absolute_urls' => str_contains(
        (string) @file_get_contents(BASE_PATH . '/public/sitemap.xml'),
        'https://www.desnkygroup.com/'
    ),
    'robots_blocks_admin' => str_contains((string) @file_get_contents(BASE_PATH . '/public/robots.txt'), 'Disallow: /admin'),
    'css_under_250kb' => filesize(BASE_PATH . '/public/assets/css/main.min.css') < 250000,
    'analytics_under_20kb' => filesize(BASE_PATH . '/public/assets/js/analytics.js') < 20000,
];

$report = [
    'generated_at' => date(DATE_ATOM),
    'checks' => $checks,
    'passed' => !in_array(false, $checks, true),
    'performance_budget' => [
        'main_min_css_bytes' => filesize(BASE_PATH . '/public/assets/css/main.min.css'),
        'analytics_js_bytes' => filesize(BASE_PATH . '/public/assets/js/analytics.js'),
    ],
];

$reportPath = BASE_PATH . '/storage/logs/phase10-qa-audit.json';
if (!is_dir(dirname($reportPath))) {
    mkdir(dirname($reportPath), 0775, true);
}

file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

foreach ($checks as $name => $passed) {
    echo ($passed ? '[PASS] ' : '[FAIL] ') . $name . PHP_EOL;
}

echo 'Report: storage/logs/phase10-qa-audit.json' . PHP_EOL;

exit($report['passed'] ? 0 : 1);
