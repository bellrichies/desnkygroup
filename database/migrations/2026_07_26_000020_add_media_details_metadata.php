<?php

namespace Database\Migrations;

use App\Database\Migration;

class AddMediaDetailsMetadata extends Migration
{
    public function up(): void
    {
        $columns = array_column($this->connection->query("SHOW COLUMNS FROM `media_library`"), 'Field');
        $definitions = [
            'seo_description' => "ADD COLUMN `seo_description` VARCHAR(320) NULL AFTER `alt_text`",
            'caption' => "ADD COLUMN `caption` TEXT NULL AFTER `seo_description`",
            'tags' => "ADD COLUMN `tags` VARCHAR(500) NULL AFTER `caption`",
            'width' => "ADD COLUMN `width` INT UNSIGNED NULL AFTER `size`",
            'height' => "ADD COLUMN `height` INT UNSIGNED NULL AFTER `width`",
        ];

        foreach ($definitions as $column => $definition) {
            if (!in_array($column, $columns, true)) {
                $this->connection->execute("ALTER TABLE `media_library` {$definition}");
            }
        }

        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        foreach ($this->connection->query(
            "SELECT id, path FROM media_library WHERE media_type = 'image' AND (width IS NULL OR height IS NULL)"
        ) as $item) {
            $relativePath = (string) ($item['path'] ?? '');
            if (!str_starts_with($relativePath, '/uploads/media/')) {
                continue;
            }
            $dimensions = @getimagesize($basePath . '/public' . $relativePath);
            if (!is_array($dimensions)) {
                continue;
            }
            $this->connection->update(
                "UPDATE media_library SET width = ?, height = ? WHERE id = ?",
                [(int) $dimensions[0], (int) $dimensions[1], (int) $item['id']]
            );
        }
    }

    public function down(): void
    {
        foreach (['height', 'width', 'tags', 'caption', 'seo_description'] as $column) {
            if ($this->connection->query("SHOW COLUMNS FROM `media_library` LIKE '{$column}'") !== []) {
                $this->connection->execute("ALTER TABLE `media_library` DROP COLUMN `{$column}`");
            }
        }
    }
}
