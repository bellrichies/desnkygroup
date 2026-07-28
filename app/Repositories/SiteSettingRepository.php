<?php

namespace App\Repositories;

/**
 * Data access for public site settings.
 */
class SiteSettingRepository extends BaseRepository
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function allKeyed(): array
    {
        $rows = $this->connection->query(
            "SELECT setting_key, setting_value, setting_type, is_public, updated_at
             FROM site_settings
             ORDER BY setting_key ASC"
        );

        $settings = [];
        foreach ($rows as $row) {
            $settings[(string) $row['setting_key']] = $row;
        }

        return $settings;
    }

    /**
     * @return array<string, mixed>
     */
    public function publicSettings(): array
    {
        $rows = $this->connection->query(
            "SELECT setting_key, setting_value, setting_type
             FROM site_settings
             WHERE is_public = 1"
        );

        $settings = [];
        foreach ($rows as $row) {
            $settings[(string) $row['setting_key']] = $this->castValue(
                (string) ($row['setting_value'] ?? ''),
                (string) ($row['setting_type'] ?? 'string')
            );
        }

        return $settings;
    }

    /**
     * @param array<int, array<string, mixed>> $settings
     */
    public function upsertMany(array $settings): void
    {
        $this->connection->beginTransaction();

        try {
            foreach ($settings as $setting) {
                $this->connection->insert(
                    "INSERT INTO site_settings (setting_key, setting_value, setting_type, is_public)
                     VALUES (?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                        setting_value = VALUES(setting_value),
                        setting_type = VALUES(setting_type),
                        is_public = VALUES(is_public)",
                    [
                        $setting['setting_key'],
                        $setting['setting_value'],
                        $setting['setting_type'] ?? 'string',
                        !empty($setting['is_public']) ? 1 : 0,
                    ]
                );
            }

            $this->connection->commit();
        } catch (\Throwable $exception) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }

            throw $exception;
        }
    }

    private function castValue(string $value, string $type): mixed
    {
        return match ($type) {
            'boolean', 'bool' => in_array(strtolower($value), ['1', 'true', 'yes'], true),
            'integer', 'int' => (int) $value,
            'json' => json_decode($value, true) ?: [],
            default => $value,
        };
    }
}
