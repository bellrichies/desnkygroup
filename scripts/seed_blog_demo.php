<?php

/**
 * Seed Blog Demo Data
 *
 * Usage: php scripts/seed_blog_demo.php
 */

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

\App\Config::load(BASE_PATH . '/config');

$config = \App\Config::get('database.connections.mysql');
$connection = new \App\Database\Connection($config);

try {
    $connection->beginTransaction();

    echo "Seeding demo blog data...\n";

    // Authors
    $authorId = $connection->insert(
        "INSERT INTO blog_authors (admin_user_id, display_name, slug, title, bio, email) VALUES (?, ?, ?, ?, ?, ?)",
        [null, 'Demo Author', 'demo-author', 'Demo Author', 'This is a seeded demo author.', 'demo@example.com']
    );

    // Categories
    $categories = [
        ['News', 'news', 'Latest news and updates'],
        ['Guides', 'guides', 'How-to guides and tutorials'],
        ['Announcements', 'announcements', 'Product announcements']
    ];

    $categoryIds = [];
    foreach ($categories as $cat) {
        $categoryIds[] = $connection->insert(
            "INSERT INTO blog_categories (name, slug, description, is_active, sort_order) VALUES (?, ?, ?, ?, ?)",
            [$cat[0], $cat[1], $cat[2], 1, 0]
        );
    }

    // Tags
    $tags = [
        ['php', 'php'],
        ['tutorial', 'tutorial'],
        ['release', 'release']
    ];

    $tagIds = [];
    foreach ($tags as $tag) {
        $tagIds[] = $connection->insert(
            "INSERT INTO blog_tags (name, slug) VALUES (?, ?)",
            [$tag[0], $tag[1]]
        );
    }

    // Posts
    $now = (new DateTime())->format('Y-m-d H:i:s');

    $posts = [
        [
            'title' => 'Welcome to the Demo Blog',
            'slug' => 'welcome-to-demo-blog',
            'excerpt' => 'An introduction to the demo blog seeded for testing.',
            'content' => '<p>This is a demo blog post created by the seeder for testing and development purposes.</p>',
            'status' => 'published',
            'is_featured' => 1,
            'published_at' => $now,
        ],
        [
            'title' => 'How to Use the CMS',
            'slug' => 'how-to-use-cms',
            'excerpt' => 'A short guide on using the CMS.',
            'content' => '<p>Use this article to learn how to create and manage content in the CMS.</p>',
            'status' => 'published',
            'is_featured' => 0,
            'published_at' => $now,
        ],
    ];

    $postIds = [];
    foreach ($posts as $p) {
        $postId = $connection->insert(
            "INSERT INTO blog_posts (author_id, title, slug, excerpt, content, status, is_featured, published_at, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$authorId, $p['title'], $p['slug'], $p['excerpt'], $p['content'], $p['status'], $p['is_featured'], $p['published_at'], null, null]
        );
        $postIds[] = $postId;

        // Attach first category and first tag for demo
        $connection->insert(
            "INSERT INTO blog_post_categories (post_id, category_id, is_primary) VALUES (?, ?, ?)",
            [$postId, $categoryIds[0], 1]
        );

        $connection->insert(
            "INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)",
            [$postId, $tagIds[0]]
        );
    }

    $connection->commit();

    echo "Seeding complete. Inserted: Authors=1, Categories=" . count($categoryIds) . ", Tags=" . count($tagIds) . ", Posts=" . count($postIds) . "\n";

} catch (\Exception $e) {
    if ($connection->inTransaction()) {
        $connection->rollBack();
    }
    echo "Error seeding demo data: " . $e->getMessage() . "\n";
    exit(1);
}

exit(0);
