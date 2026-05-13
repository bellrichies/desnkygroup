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
        $data['created_by'] = $data['created_by'] ?? 1;

        return $this->products->create($data);
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
}
