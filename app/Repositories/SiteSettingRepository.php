<?php

namespace App\Repositories;

/**
 * Data access for public site settings.
 */
class SiteSettingRepository extends BaseRepository
{
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
