<?php

namespace App\Services;

use App\Repositories\ServiceHeroRepository;
use InvalidArgumentException;

class ServiceHeroService
{
    public function __construct(private ServiceHeroRepository $heroes)
    {
    }

    public function forService(int $serviceId): array
    {
        return $this->heroes->forService($serviceId);
    }

    public function activeForService(int $serviceId): array
    {
        return $this->heroes->activeForService($serviceId);
    }

    public function find(int $id, int $serviceId): ?array
    {
        return $this->heroes->find($id, $serviceId);
    }

    public function create(array $data): int
    {
        return $this->heroes->create($this->validated($data));
    }

    public function update(int $id, int $serviceId, array $data): bool
    {
        return $this->heroes->update($id, $serviceId, $this->validated($data));
    }

    public function delete(int $id, int $serviceId): bool
    {
        return $this->heroes->delete($id, $serviceId);
    }

    public function toggle(int $id, int $serviceId): bool
    {
        return $this->heroes->toggle($id, $serviceId);
    }

    public function reorder(int $serviceId, array $orders): bool
    {
        return $this->heroes->reorder($serviceId, $orders);
    }

    private function validated(array $data): array
    {
        $clean = [
            'service_id' => (int) ($data['service_id'] ?? 0),
            'heading' => trim((string) ($data['heading'] ?? '')),
            'subheading' => $this->nullable($data['subheading'] ?? null),
            'description' => $this->nullable($data['description'] ?? null),
            'media_type' => (string) ($data['media_type'] ?? 'image'),
            'background_media' => $this->nullable($data['background_media'] ?? null),
            'primary_cta_label' => $this->nullable($data['primary_cta_label'] ?? null),
            'primary_cta_url' => $this->nullable($data['primary_cta_url'] ?? null),
            'secondary_cta_label' => $this->nullable($data['secondary_cta_label'] ?? null),
            'secondary_cta_url' => $this->nullable($data['secondary_cta_url'] ?? null),
            'sort_order' => max(0, (int) ($data['sort_order'] ?? 0)),
            'is_visible' => !empty($data['is_visible']) ? 1 : 0,
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'created_by' => isset($data['created_by']) ? (int) $data['created_by'] : null,
        ];

        if ($clean['service_id'] < 1 || $clean['heading'] === '') {
            throw new InvalidArgumentException('A service and hero heading are required.');
        }
        if (mb_strlen($clean['heading']) > 255 || mb_strlen((string) $clean['subheading']) > 500) {
            throw new InvalidArgumentException('Hero heading or subheading is too long.');
        }
        if (!in_array($clean['media_type'], ['image', 'video'], true)) {
            throw new InvalidArgumentException('Choose a supported background media type.');
        }
        if ($clean['background_media'] !== null && !$this->allowedMedia($clean['background_media'])) {
            throw new InvalidArgumentException('Select an uploaded media file or provide a valid HTTPS media URL.');
        }
        $this->validateCta($clean['primary_cta_label'], $clean['primary_cta_url'], 'Primary');
        $this->validateCta($clean['secondary_cta_label'], $clean['secondary_cta_url'], 'Secondary');

        return $clean;
    }

    private function validateCta(?string $label, ?string $url, string $name): void
    {
        if (($label === null) !== ($url === null)) {
            throw new InvalidArgumentException("{$name} CTA text and link must be supplied together.");
        }
        if ($label !== null && mb_strlen($label) > 120) {
            throw new InvalidArgumentException("{$name} CTA text is too long.");
        }
        if ($url !== null && !$this->allowedUrl($url)) {
            throw new InvalidArgumentException("{$name} CTA link must be an in-page anchor, internal path, or valid HTTP(S) URL.");
        }
    }

    private function allowedUrl(string $url): bool
    {
        if (preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $url) === 1) {
            return true;
        }

        return (str_starts_with($url, '/') && !str_starts_with($url, '//'))
            || (filter_var($url, FILTER_VALIDATE_URL) !== false
                && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true));
    }

    private function allowedMedia(string $path): bool
    {
        return str_starts_with($path, '/uploads/media/')
            || str_starts_with($path, '/assets/')
            || $this->allowedUrl($path);
    }

    private function nullable(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }
}
