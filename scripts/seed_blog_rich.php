<?php
/**
 * Rich blog seeder: authors, images, scheduled posts
 * Usage: php scripts/seed_blog_rich.php
 */
require_once __DIR__ . '/../vendor/autoload.php';
\App\Config::load(__DIR__ . '/../config');
$c = new \App\Database\Connection(\App\Config::get('database.connections.mysql'));

$faker = null;
if (class_exists('Faker\\Factory')) {
    $faker = Faker\Factory::create();
}

// Create authors
$authors = [
    ['display_name' => 'Admin Editor', 'slug' => 'admin-editor', 'email' => 'admin@example.com'],
    ['display_name' => 'Jane Doe', 'slug' => 'jane-doe', 'email' => 'jane@example.com'],
    ['display_name' => 'John Smith', 'slug' => 'john-smith', 'email' => 'john@example.com'],
];
$authorIds = [];
foreach ($authors as $a) {
    $c->execute("INSERT INTO blog_authors (display_name, slug, email, is_active) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE display_name = VALUES(display_name), email = VALUES(email)", [$a['display_name'], $a['slug'], $a['email']]);
    $row = $c->queryOne('SELECT id FROM blog_authors WHERE slug = ?', [$a['slug']]);
    $authorIds[] = (int) ($row['id'] ?? 0);
}

// find an admin user id to set created_by/updated_by where possible
$adminRow = $c->queryOne('SELECT id FROM admin_users LIMIT 1');
$adminUserId = $adminRow ? (int) $adminRow['id'] : null;

// Reuse categories and tags created earlier; ensure a few exist
$categories = ['News','Guides','Announcements','Engineering','HSE'];
$categoryIds = [];
foreach ($categories as $name) {
    $slug = strtolower(str_replace(' ', '-', $name));
    $c->execute("INSERT INTO blog_categories (name, slug, description, is_active) VALUES (?, ?, ?, 1) ON DUPLICATE KEY UPDATE name = VALUES(name)", [$name, $slug, $name . ' articles']);
    $row = $c->queryOne('SELECT id FROM blog_categories WHERE slug = ?', [$slug]);
    $categoryIds[] = (int) ($row['id'] ?? 0);
}

$tags = ['php','tutorial','release','safety','procurement','energy','ict','agro','ops'];
$tagIds = [];
foreach ($tags as $t) {
    $c->execute("INSERT INTO blog_tags (name, slug) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name)", [ucfirst($t), $t]);
    $row = $c->queryOne('SELECT id FROM blog_tags WHERE slug = ?', [$t]);
    $tagIds[] = (int) ($row['id'] ?? 0);
}

// Create 20 richer posts
for ($i = 1; $i <= 20; $i++) {
    $title = $faker ? $faker->sentence(6) : "Rich Demo Post {$i}";
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($title)));
    $excerpt = $faker ? $faker->paragraph(2) : "Excerpt for {$title}";
    $content = $faker ? '<p>' . implode('</p><p>', $faker->paragraphs(5)) . '</p>' : '<p>Demo content</p>';
    $authorId = $authorIds[array_rand($authorIds)];
    $categoryId = $categoryIds[array_rand($categoryIds)];
    $postTagIds = array_values(array_filter(array_unique([$tagIds[array_rand($tagIds)], $tagIds[array_rand($tagIds)]])));

    // Randomly set some posts as scheduled in future
    $isScheduled = ($i % 5 === 0);
    $publishedAt = $isScheduled ? null : date('Y-m-d H:i:s', strtotime('-' . rand(0, 30) . ' days'));
    $scheduledAt = $isScheduled ? date('Y-m-d H:i:s', strtotime('+' . rand(1, 10) . ' days')) : null;

    $featured = 'https://picsum.photos/seed/' . urlencode($slug) . '/1200/600';

    // Insert or update post by slug
    $existing = $c->queryOne('SELECT id FROM blog_posts WHERE slug = ?', [$slug]);
    if ($existing) {
        $postId = (int) $existing['id'];
        $c->update('UPDATE blog_posts SET title = ?, excerpt = ?, content = ?, featured_image = ?, featured_image_alt = ?, status = ?, published_at = ?, scheduled_at = ?, author_id = ? WHERE id = ?', [$title, $excerpt, $content, $featured, $title, $isScheduled ? 'scheduled' : 'published', $publishedAt, $scheduledAt, $authorId, $postId]);
    } else {
        $postId = $c->insert('INSERT INTO blog_posts (author_id, title, slug, excerpt, content, featured_image, featured_image_alt, status, published_at, scheduled_at, created_by, updated_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$authorId, $title, $slug, $excerpt, $content, $featured, $title, $isScheduled ? 'scheduled' : 'published', $publishedAt, $scheduledAt, $adminUserId, $adminUserId]);
    }

    // categories
    $c->delete('DELETE FROM blog_post_categories WHERE post_id = ?', [$postId]);
    $c->insert('INSERT INTO blog_post_categories (post_id, category_id, is_primary) VALUES (?, ?, 1)', [$postId, $categoryId]);

    // tags
    $c->delete('DELETE FROM blog_post_tags WHERE post_id = ?', [$postId]);
    foreach ($postTagIds as $tid) {
        $c->insert('INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)', [$postId, $tid]);
    }

    // media
    $c->delete('DELETE FROM blog_media WHERE post_id = ?', [$postId]);
    $c->insert('INSERT INTO blog_media (post_id, path, alt_text, caption, sort_order) VALUES (?, ?, ?, ?, ?)', [$postId, $featured, $title, $excerpt, 0]);

    echo "Rich seeded post {$postId} - {$slug}\n";
}

echo "Rich seeding complete.\n";
exit(0);
