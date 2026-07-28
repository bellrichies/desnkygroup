<?php

namespace App\Services;

use App\Repositories\SiteSettingRepository;
use InvalidArgumentException;

/**
 * Validates and persists CMS-managed site settings.
 */
class SiteSettingService extends BaseService
{
    public function __construct(
        private SiteSettingRepository $settings,
        private ?CacheService $cache = null
    ) {
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function groupedFields(): array
    {
        return [
            'Brand' => [
                ['key' => 'site.name', 'label' => 'Site name', 'type' => 'text', 'required' => true, 'default' => 'Desnky Global Resources Ltd'],
                ['key' => 'site.url', 'label' => 'Site URL', 'type' => 'url', 'required' => true, 'default' => 'https://www.desnkygroup.com/'],
            ],
            'Contact' => [
                ['key' => 'site.email', 'label' => 'Public email', 'type' => 'email', 'required' => true, 'default' => 'info@desnkygroup.com'],
                ['key' => 'site.phone', 'label' => 'Phone link value', 'type' => 'text', 'required' => true, 'default' => '+2340000000000', 'help' => 'Used for tel: links. Keep country code.'],
                ['key' => 'site.phone_display', 'label' => 'Phone display label', 'type' => 'text', 'required' => true, 'default' => '+234 000 000 0000'],
                ['key' => 'site.whatsapp', 'label' => 'WhatsApp number', 'type' => 'text', 'required' => true, 'default' => '2340000000000', 'help' => 'Digits only, including country code.'],
                ['key' => 'site.address', 'label' => 'Address', 'type' => 'textarea', 'required' => true, 'default' => 'Lagos, Nigeria'],
                ['key' => 'site.hours', 'label' => 'Business hours', 'type' => 'text', 'required' => false, 'default' => 'Mon-Fri, 9:00 AM - 5:00 PM'],
            ],
            'Social Links' => [
                ['key' => 'social.linkedin', 'label' => 'LinkedIn URL', 'type' => 'url', 'required' => false, 'default' => 'https://www.linkedin.com/company/desnkygroup'],
                ['key' => 'social.x', 'label' => 'X URL', 'type' => 'url', 'required' => false, 'default' => 'https://x.com/desnkygroup'],
                ['key' => 'social.facebook', 'label' => 'Facebook URL', 'type' => 'url', 'required' => false, 'default' => 'https://www.facebook.com/desnkygroup'],
                ['key' => 'social.instagram', 'label' => 'Instagram URL', 'type' => 'url', 'required' => false, 'default' => 'https://www.instagram.com/desnkygroup'],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function values(): array
    {
        $stored = $this->settings->allKeyed();
        $values = [];

        foreach ($this->flatFields() as $field) {
            $key = (string) $field['key'];
            $values[$key] = (string) ($stored[$key]['setting_value'] ?? $field['default'] ?? '');
        }

        return $values;
    }

    public function update(array $input): void
    {
        $payload = [];

        foreach ($this->flatFields() as $field) {
            $key = (string) $field['key'];
            $value = trim((string) ($input[$key] ?? ''));

            if (!empty($field['required']) && $value === '') {
                throw new InvalidArgumentException($field['label'] . ' is required.');
            }

            if ($value !== '' && $field['type'] === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException($field['label'] . ' must be a valid email address.');
            }

            if ($value !== '' && $field['type'] === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException($field['label'] . ' must be a valid URL.');
            }

            if ($key === 'site.whatsapp') {
                $value = preg_replace('/\D+/', '', $value) ?? '';
            }

            $payload[] = [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => 'string',
                'is_public' => 1,
            ];
        }

        $this->settings->upsertMany($payload);
        $this->cache?->flushTag('settings');
    }

    public function clearCache(): void
    {
        $this->cache?->flushAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function flatFields(): array
    {
        return array_merge(...array_values($this->groupedFields()));
    }
}
