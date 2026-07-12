<?php
/**
 * Seed 30 demo blog posts with categories and tags
 * Usage: php scripts/seed_blog_30.php
 */
require_once __DIR__ . '/../vendor/autoload.php';
\App\Config::load(__DIR__ . '/../config');
$c = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));

// Ensure some categories exist
$categories = ['News', 'Guides', 'Announcements', 'Engineering', 'HSE', 'Procurement'];
$categoryIds = [];
foreach ($categories as $slugName) {
    $slug = strtolower(str_replace(' ', '-', $slugName));
    $c->execute("INSERT INTO blog_categories (name, slug, description, is_active) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE name = VALUES(name)", [$slugName, $slug, $slugName . ' articles']);
    $row = $c->queryOne('SELECT id FROM blog_categories WHERE slug = ?', [$slug]);
    $categoryIds[] = (int) ($row['id'] ?? 0);
}

// Ensure some tags exist
$tags = ['php','tutorial','release','safety','procurement','energy','ict'];
$tagIds = [];
foreach ($tags as $t) {
    $c->execute("INSERT INTO blog_tags (name, slug) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)", [ucfirst($t), $t]);
    $row = $c->queryOne('SELECT id FROM blog_tags WHERE slug = ?', [$t]);
    $tagIds[] = (int) ($row['id'] ?? 0);
}

$faker = null;
if (class_exists('Faker\Factory')) {
    $faker = Faker\Factory::create();
}

$admin = $c->queryOne('SELECT id FROM admin_users WHERE email = ?', ['admin@example.com']);
$adminId = (int) ($admin['id'] ?? 1);

for ($i = 1; $i <= 30; $i++) {
    $title = ($faker ? $faker->sentence(6) : "Demo Post {$i}");
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($title)));
    $excerpt = ($faker ? $faker->paragraph(2) : "Demo excerpt for post {$i}.");
    $content = ($faker ? '<p>' . implode('</p><p>', $faker->paragraphs(4)) . '</p>' : '<p>Demo content</p>');
    $categoryId = $categoryIds[array_rand($categoryIds)];
    $postTagIds = array_values(array_filter(array_unique([$tagIds[array_rand($tagIds)], $tagIds[array_rand($tagIds)]])));

    $postId = $c->insert(
        "INSERT INTO blog_posts (author_id, title, slug, excerpt, content, status, is_featured, published_at, created_by, updated_by) VALUES (?, ?, ?, ?, ?, 'published', 0, NOW(), ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title), excerpt = VALUES(excerpt), content = VALUES(content), status = 'published'",
        [$adminId, $title, $slug, $excerpt, $content, $adminId, $adminId]
    );

    // categories
    $c->delete('DELETE FROM blog_post_categories WHERE post_id = ?', [$postId]);
    $c->insert('INSERT INTO blog_post_categories (post_id, category_id, is_primary) VALUES (?, ?, 1)', [$postId, $categoryId]);

    // tags
    $c->delete('DELETE FROM blog_post_tags WHERE post_id = ?', [$postId]);
    foreach ($postTagIds as $tid) {
        $c->insert('INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)', [$postId, $tid]);
    }

    echo "Seeded post {$postId} - {$slug}\n";
}

echo "Seeding complete.\n";
exit(0);
