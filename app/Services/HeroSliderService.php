<?php

namespace App\Services;

use App\Repositories\HeroSliderRepository;
use InvalidArgumentException;

/**
 * Business rules for homepage hero slider management.
 */
class HeroSliderService extends BaseService
{
    public function __construct(private HeroSliderRepository $repo)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->repo->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function active(): array
    {
        return $this->repo->active();
    }

    public function find(int $id): ?array
    {
        return $this->repo->find($id);
    }

    public function create(array $data): int
    {
        return $this->repo->create($this->validated($data));
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $this->validated($data));
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function toggleActive(int $id): bool
    {
        return $this->repo->toggleActive($id);
    }

    /**
     * @param array<string, mixed> $orders
     */
    public function reorder(array $orders): bool
    {
        $clean = [];

        foreach ($orders as $id => $sortOrder) {
            $id = (int) $id;
            if ($id <= 0) {
                continue;
            }

            $clean[$id] = (int) $sortOrder;
        }

        return $this->repo->updateSortOrders($clean);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function validated(array $data): array
    {
        $clean = [
            'background_image' => trim((string) ($data['background_image'] ?? '')),
            'heading' => trim((string) ($data['heading'] ?? '')),
            'caption' => $this->emptyToNull($data['caption'] ?? null),
            'primary_cta_label' => $this->emptyToNull($data['primary_cta_label'] ?? null),
            'primary_cta_url' => $this->emptyToNull($data['primary_cta_url'] ?? null),
            'secondary_cta_label' => $this->emptyToNull($data['secondary_cta_label'] ?? null),
            'secondary_cta_url' => $this->emptyToNull($data['secondary_cta_url'] ?? null),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => !empty($data['is_active']) ? 1 : 0,
            'created_by' => isset($data['created_by']) ? (int) $data['created_by'] : null,
        ];

        if ($clean['background_image'] === '') {
            throw new InvalidArgumentException('A background image is required.');
        }

        if (strlen($clean['background_image']) > 255 || !$this->isAllowedImagePath($clean['background_image'])) {
            throw new InvalidArgumentException('Background image must be selected from the media library or uploaded through this form.');
        }

        if ($clean['heading'] === '') {
            throw new InvalidArgumentException('Heading is required.');
        }

        if (strlen($clean['heading']) > 255) {
            throw new InvalidArgumentException('Heading must not exceed 255 characters.');
        }

        if ($clean['caption'] !== null && strlen($clean['caption']) > 1000) {
            throw new InvalidArgumentException('Caption must not exceed 1000 characters.');
        }

        $this->validateCtaPair(
            $clean['primary_cta_label'],
            $clean['primary_cta_url'],
            'primary'
        );
        $this->validateCtaPair(
            $clean['secondary_cta_label'],
            $clean['secondary_cta_url'],
            'secondary'
        );

        return $clean;
    }

    private function emptyToNull(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function validateCtaPair(?string $label, ?string $url, string $name): void
    {
        if (($label === null && $url !== null) || ($label !== null && $url === null)) {
            throw new InvalidArgumentException("Both {$name} CTA label and URL are required when one is provided.");
        }

        if ($label !== null && strlen($label) > 120) {
            throw new InvalidArgumentException(ucfirst($name) . ' CTA label must not exceed 120 characters.');
        }

        if ($url !== null) {
            if (strlen($url) > 255) {
                throw new InvalidArgumentException(ucfirst($name) . ' CTA URL must not exceed 255 characters.');
            }

            if (!$this->isAllowedDestination($url)) {
                throw new InvalidArgumentException(ucfirst($name) . ' CTA URL must be an internal path or valid URL.');
            }
        }
    }

    private function isAllowedDestination(string $url): bool
    {
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return true;
        }

        return filter_var($url, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }

    private function isAllowedImagePath(string $path): bool
    {
        if (str_starts_with($path, '/uploads/media/') || str_starts_with($path, '/assets/images/')) {
            return true;
        }

        return filter_var($path, FILTER_VALIDATE_URL) !== false
            && in_array(parse_url($path, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
