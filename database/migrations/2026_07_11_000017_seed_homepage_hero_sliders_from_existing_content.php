<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * Migrates existing homepage hero/service slider content into the dedicated hero slider table.
 */
class SeedHomepageHeroSlidersFromExistingContent extends Migration
{
    public function up(): void
    {
        $existing = $this->connection->queryOne(
            "SELECT COUNT(*) AS aggregate FROM homepage_hero_sliders"
        );

        if ((int) ($existing['aggregate'] ?? 0) > 0) {
            return;
        }

        $this->seedHomeHero();
        $this->seedServiceSlides();
    }

    public function down(): void
    {
        $this->connection->delete(
            "DELETE FROM homepage_hero_sliders WHERE created_by IS NULL"
        );
    }

    private function seedHomeHero(): void
    {
        $row = $this->connection->queryOne(
            "SELECT ps.heading, ps.body
             FROM page_sections ps
             INNER JOIN pages p ON p.id = ps.page_id
             WHERE p.slug = ? AND ps.section_key = ?
             ORDER BY ps.sort_order ASC, ps.id ASC
             LIMIT 1",
            ['home', 'hero']
        );

        if ($row === null) {
            return;
        }

        $body = $this->decodeBody((string) ($row['body'] ?? ''));
        $image = trim((string) ($body['image'] ?? ''));
        $heading = trim((string) ($row['heading'] ?? ''));

        if ($image === '' || $heading === '') {
            return;
        }

        $this->insertSlide([
            'background_image' => $image,
            'heading' => $heading,
            'caption' => $body['text'] ?? null,
            'primary_cta_label' => $body['primary_cta_label'] ?? null,
            'primary_cta_url' => $body['primary_cta_url'] ?? null,
            'secondary_cta_label' => $body['secondary_cta_label'] ?? null,
            'secondary_cta_url' => $body['secondary_cta_url'] ?? null,
            'sort_order' => 10,
        ]);
    }

    private function seedServiceSlides(): void
    {
        $services = $this->connection->query(
            "SELECT title, slug, summary, featured_image, sort_order
             FROM services
             WHERE is_published = 1 AND deleted_at IS NULL AND featured_image IS NOT NULL AND featured_image <> ''
             ORDER BY sort_order ASC, title ASC"
        );

        foreach ($services as $service) {
            $this->insertSlide([
                'background_image' => (string) $service['featured_image'],
                'heading' => (string) $service['title'],
                'caption' => (string) ($service['summary'] ?? ''),
                'primary_cta_label' => 'Explore service',
                'primary_cta_url' => '/services/' . (string) $service['slug'],
                'secondary_cta_label' => 'Request a Quote',
                'secondary_cta_url' => '/contact?service=' . (string) $service['slug'],
                'sort_order' => 20 + (int) ($service['sort_order'] ?? 0),
            ]);
        }
    }

    /**
     * @param array<string, mixed> $slide
     */
    private function insertSlide(array $slide): void
    {
        $this->connection->insert(
            "INSERT INTO homepage_hero_sliders
                (background_image, heading, caption, primary_cta_label, primary_cta_url,
                 secondary_cta_label, secondary_cta_url, sort_order, is_active, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NULL)",
            [
                $slide['background_image'],
                $slide['heading'],
                $slide['caption'] ?? null,
                $slide['primary_cta_label'] ?? null,
                $slide['primary_cta_url'] ?? null,
                $slide['secondary_cta_label'] ?? null,
                $slide['secondary_cta_url'] ?? null,
                (int) ($slide['sort_order'] ?? 0),
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeBody(string $body): array
    {
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : [];
    }
}
