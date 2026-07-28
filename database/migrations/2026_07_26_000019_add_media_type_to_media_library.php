<?php

namespace Database\Migrations;

use App\Database\Migration;

class AddMediaTypeToMediaLibrary extends Migration
{
    public function up(): void
    {
        $columns = $this->connection->query("SHOW COLUMNS FROM `media_library` LIKE 'media_type'");
        if ($columns === []) {
            $this->connection->execute(
                "ALTER TABLE `media_library`
                 ADD COLUMN `media_type` VARCHAR(20) NOT NULL DEFAULT 'image' AFTER `mime_type`,
                 ADD KEY `key_media_library_type_created` (`media_type`, `created_at`)"
            );
        }

        $this->connection->execute("
            UPDATE `media_library`
            SET `media_type` = CASE
                WHEN `mime_type` LIKE 'image/%' THEN 'image'
                WHEN `mime_type` LIKE 'video/%' THEN 'video'
                ELSE 'document'
            END
        ");
    }

    public function down(): void
    {
        $columns = $this->connection->query("SHOW COLUMNS FROM `media_library` LIKE 'media_type'");
        if ($columns !== []) {
            $this->connection->execute("ALTER TABLE `media_library` DROP COLUMN `media_type`");
        }
    }
}
