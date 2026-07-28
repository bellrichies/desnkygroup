<?php

namespace App\Services;

use App\Repositories\ProductRepository;

/**
 * ProductService - Business logic for products.
 */
class ProductService extends BaseService
{
    private ProductRepository $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    /**
     * Get active products, optionally filtered by category.
     *
     * @param int|null $categoryId Category ID.
     * @return array
     */
    public function getActive(?int $categoryId = null): array
    {
        return $categoryId === null
            ? $this->products->active()
            : $this->products->byCategory($categoryId);
    }

    /**
     * Get active product by ID.
     *
     * @param int $id Product ID.
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->products->findActive($id);
    }

    /**
     * Get active product by slug.
     *
     * @param string $slug Product slug.
     * @return array|null
     */
    public function getBySlug(string $slug): ?array
    {
        return $this->products->findActiveBySlug($slug);
    }

    /**
     * Search active products.
     *
     * @param string $query Search query.
     * @return array
     */
    public function search(string $query): array
    {
        return $this->products->search($query);
    }

    /**
     * Get products below their reorder threshold.
     *
     * @return array
     */
    public function getLowStock(): array
    {
        return $this->products->lowStock();
    }

    /**
     * Create a product.
     *
     * @param array $data Product data.
     * @return int Product ID.
     */
    public function create(array $data): int
    {
        $data = $this->prepare($data);
        $data['created_by'] = $data['created_by'] ?? 1;

        $id = $this->products->create($data);
        $this->products->syncImages($id, $data['gallery']);

        return $id;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(array $filters = []): array
    {
        return $this->products->filterAdmin($filters);
    }

    public function find(int $id): ?array
    {
        return $this->products->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->prepare($data);
        $updated = $this->products->update($id, $data);
        $this->products->syncImages($id, $data['gallery']);

        return $updated;
    }

    public function delete(int $id): bool
    {
        return $this->products->softDelete($id);
    }

    /**
     * Update product inventory.
     *
     * @param int $id Product ID.
     * @param int $quantity New quantity.
     * @return bool
     */
    public function updateInventory(int $id, int $quantity): bool
    {
        return $this->products->updateInventory($id, $quantity);
    }

    public function reserveInventory(int $id, int $quantity, string $reference): bool
    {
        return $this->products->decrementStock($id, $quantity, $reference);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function inventoryHistory(int $productId): array
    {
        return $this->products->inventoryHistory($productId);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function images(int $productId): array
    {
        return $this->products->imagesForProduct($productId);
    }

    /**
     * @return array<string, mixed>
     */
    private function prepare(array $data): array
    {
        $name = trim((string) ($data['name'] ?? ''));

        if ($name === '') {
            throw new \InvalidArgumentException('Product name is required.');
        }

        $price = (float) ($data['price'] ?? 0);
        if ($price <= 0) {
            throw new \InvalidArgumentException('Product price must be greater than zero.');
        }

        $featuredImage = trim((string) ($data['featured_image'] ?? ''));
        $paths = is_array($data['gallery_paths'] ?? null) ? $data['gallery_paths'] : [];
        $alts = is_array($data['gallery_alt_texts'] ?? null) ? $data['gallery_alt_texts'] : [];
        $gallery = [];
        $seen = [];
        foreach ($paths as $index => $path) {
            $path = trim((string) $path);
            if ($path === '' || isset($seen[$path])) {
                continue;
            }
            $seen[$path] = true;
            $gallery[] = [
                'path' => $path,
                'alt_text' => trim((string) ($alts[$index] ?? '')) ?: $name,
            ];
        }
        if ($featuredImage !== '' && !isset($seen[$featuredImage])) {
            array_unshift($gallery, ['path' => $featuredImage, 'alt_text' => $name]);
        }
        usort($gallery, static fn (array $left, array $right): int =>
            ($left['path'] === $featuredImage ? 0 : 1) <=> ($right['path'] === $featuredImage ? 0 : 1)
        );
        $gallery = array_slice($gallery, 0, 5);

        return [
            'name' => $name,
            'slug' => $this->slug((string) ($data['slug'] ?? $name)),
            'description' => trim((string) ($data['description'] ?? '')),
            'short_description' => trim((string) ($data['short_description'] ?? '')),
            'price' => $price,
            'discount_price' => (float) ($data['discount_price'] ?? 0),
            'cost_price' => (float) ($data['cost_price'] ?? 0),
            'quantity_in_stock' => max(0, (int) ($data['quantity_in_stock'] ?? 0)),
            'reorder_level' => max(0, (int) ($data['reorder_level'] ?? 10)),
            'sku' => trim((string) ($data['sku'] ?? '')),
            'weight' => (float) ($data['weight'] ?? 0),
            'category_id' => (int) ($data['category_id'] ?? 0) ?: null,
            'featured_image' => $featuredImage,
            'gallery' => $gallery,
            'meta_title' => trim((string) ($data['meta_title'] ?? '')),
            'meta_description' => trim((string) ($data['meta_description'] ?? '')),
            'is_active' => isset($data['is_active']),
            'status' => in_array(($data['status'] ?? 'active'), ['active', 'inactive', 'discontinued'], true)
                ? $data['status']
                : 'active',
            'is_featured' => isset($data['is_featured']),
            'created_by' => (int) ($data['created_by'] ?? 1),
        ];
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));

        return $slug !== '' ? $slug : 'product-' . time();
    }
}
