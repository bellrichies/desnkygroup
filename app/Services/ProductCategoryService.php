<?php

namespace App\Services;

use App\Repositories\ProductCategoryRepository;

/**
 * Business logic for product category management.
 */
class ProductCategoryService extends BaseService
{
    public function __construct(private ProductCategoryRepository $categories)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->categories->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function active(): array
    {
        return $this->categories->active();
    }

    public function find(int $id): ?array
    {
        return $this->categories->find($id);
    }

    public function create(array $data): int
    {
        return $this->categories->create($this->prepare($data));
    }

    public function update(int $id, array $data): bool
    {
        return $this->categories->update($id, $this->prepare($data));
    }

    public function delete(int $id): bool
    {
        return $this->categories->delete($id);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepare(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new \InvalidArgumentException('Category name is required.');
        }

        return [
            'name' => $name,
            'slug' => $this->slug((string) ($data['slug'] ?? $name)),
            'description' => trim((string) ($data['description'] ?? '')),
            'parent_id' => (int) ($data['parent_id'] ?? 0),
            'image' => trim((string) ($data['image'] ?? '')),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'meta_title' => trim((string) ($data['meta_title'] ?? '')),
            'meta_description' => trim((string) ($data['meta_description'] ?? '')),
            'is_active' => isset($data['is_active']),
        ];
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));

        return $slug !== '' ? $slug : 'category-' . time();
    }
}
