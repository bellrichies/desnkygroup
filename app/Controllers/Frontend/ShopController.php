<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;
use App\Repositories\ProductRepository;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Public shop browsing controller for phase-three storefront pages.
 */
class ShopController extends BaseController
{
    public function index(): string
    {
        return $this->view('frontend/pages/shop/index', [
            'title' => 'Shop',
            'active' => 'shop',
            'products' => self::products(),
            'categories' => self::categories(),
            'currentCategory' => null,
            'seo' => [
                'title' => 'Shop Safety, ICT and Industrial Products | Desnky',
                'description' => 'Browse safety equipment, ICT devices, agro products and industrial supplies from Desnky Global Resources Ltd.',
                'canonical' => 'https://www.desnkygroup.com/shop',
            ],
        ]);
    }

    public function category(string $slug): string
    {
        $products = array_filter(self::products(), fn ($product) => $product['category_slug'] === $slug);
        $category = self::categories()[$slug] ?? 'Shop Category';

        return $this->view('frontend/pages/shop/index', [
            'title' => $category,
            'active' => 'shop',
            'products' => $products,
            'categories' => self::categories(),
            'currentCategory' => $slug,
            'seo' => [
                'title' => $category . ' | Desnky Shop',
                'description' => 'Browse ' . strtolower($category) . ' available through Desnky Global Resources Ltd.',
                'canonical' => 'https://www.desnkygroup.com/shop/category/' . $slug,
            ],
        ]);
    }

    public function show(string $slug): string
    {
        $product = self::products()[$slug] ?? null;

        if ($product === null) {
            http_response_code(404);

            return $this->view('frontend/pages/show', [
                'title' => 'Product Not Found',
                'content' => '<p>The requested product could not be found.</p>',
                'active' => 'shop',
            ]);
        }

        return $this->view('frontend/pages/shop/product', [
            'title' => $product['name'],
            'active' => 'shop',
            'product' => $product,
            'relatedProducts' => array_filter(
                self::products(),
                fn ($item) => $item['slug'] !== $slug && $item['category_slug'] === $product['category_slug']
            ),
            'csrf_token' => $this->csrf(),
            'seo' => [
                'title' => $product['name'] . ' | Desnky Shop',
                'description' => $product['short_description'],
                'canonical' => 'https://www.desnkygroup.com/shop/product/' . $slug,
                'image' => $product['image'],
                'type' => 'product',
                'schema' => SeoHelper::productSchema($product),
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        try {
            $rows = (new ProductRepository(DatabaseFactory::make()))->activeCategories();

            if ($rows !== []) {
                $categories = [];

                foreach ($rows as $row) {
                    $categories[(string) $row['slug']] = (string) $row['name'];
                }

                return $categories;
            }
        } catch (Throwable) {
        }

        return [
            'safety-equipment' => 'Safety Equipment',
            'industrial-supplies' => 'Industrial Supplies',
            'ict-devices' => 'ICT Devices',
            'agro-products' => 'Agro Products',
        ];
    }

    /**
     * @return array<string, array>
     */
    public static function products(): array
    {
        try {
            $rows = (new ProductRepository(DatabaseFactory::make()))->activeForShop();

            if ($rows !== []) {
                $products = [];

                foreach ($rows as $row) {
                    $slug = (string) $row['slug'];
                    $stock = (int) ($row['quantity_in_stock'] ?? 0);
                    $products[$slug] = [
                        'id' => (int) $row['id'],
                        'slug' => $slug,
                        'name' => (string) $row['name'],
                        'sku' => (string) $row['sku'],
                        'category' => (string) ($row['category_name'] ?? 'Products'),
                        'category_slug' => (string) ($row['category_slug'] ?? 'products'),
                        'price' => (float) $row['price'],
                        'old_price' => null,
                        'stock' => $stock,
                        'in_stock' => $stock > 0,
                        'short_description' => (string) ($row['short_description'] ?? ''),
                        'description' => (string) ($row['description'] ?? ''),
                        'image' => (string) ($row['display_image'] ?: 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=900&q=80'),
                    ];
                }

                return $products;
            }
        } catch (Throwable) {
        }

        return [
            'industrial-safety-helmet' => [
                'slug' => 'industrial-safety-helmet',
                'name' => 'Industrial Safety Helmet',
                'sku' => 'HSE-HELMET-01',
                'category' => 'Safety Equipment',
                'category_slug' => 'safety-equipment',
                'price' => 18500,
                'old_price' => 22000,
                'stock' => 35,
                'in_stock' => true,
                'short_description' => 'Durable protective helmet for industrial and construction teams.',
                'description' => 'A durable safety helmet suitable for industrial, engineering and site operations requiring head protection.',
                'image' => 'https://images.unsplash.com/photo-1591101501707-1264a4b14500?auto=format&fit=crop&w=900&q=80',
            ],
            'reflective-safety-vest' => [
                'slug' => 'reflective-safety-vest',
                'name' => 'Reflective Safety Vest',
                'sku' => 'HSE-VEST-02',
                'category' => 'Safety Equipment',
                'category_slug' => 'safety-equipment',
                'price' => 7500,
                'old_price' => null,
                'stock' => 80,
                'in_stock' => true,
                'short_description' => 'High-visibility vest for field, logistics and worksite personnel.',
                'description' => 'A lightweight high-visibility vest for safety identification across field and site activities.',
                'image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80',
            ],
            'industrial-toolkit' => [
                'slug' => 'industrial-toolkit',
                'name' => 'Industrial Maintenance Toolkit',
                'sku' => 'IND-TOOL-03',
                'category' => 'Industrial Supplies',
                'category_slug' => 'industrial-supplies',
                'price' => 145000,
                'old_price' => null,
                'stock' => 12,
                'in_stock' => true,
                'short_description' => 'Curated maintenance toolkit for technical teams and field support.',
                'description' => 'A practical toolkit for routine maintenance and field support tasks in industrial environments.',
                'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=900&q=80',
            ],
            'network-router-kit' => [
                'slug' => 'network-router-kit',
                'name' => 'Business Network Router Kit',
                'sku' => 'ICT-NET-04',
                'category' => 'ICT Devices',
                'category_slug' => 'ict-devices',
                'price' => 98000,
                'old_price' => 115000,
                'stock' => 9,
                'in_stock' => true,
                'short_description' => 'Router and basic network setup kit for small business connectivity.',
                'description' => 'A business network kit for office connectivity deployments and ICT support projects.',
                'image' => 'https://images.unsplash.com/photo-1600267165477-6d4cc741b379?auto=format&fit=crop&w=900&q=80',
            ],
            'processed-agro-pack' => [
                'slug' => 'processed-agro-pack',
                'name' => 'Processed Agro Supply Pack',
                'sku' => 'AGRO-PACK-05',
                'category' => 'Agro Products',
                'category_slug' => 'agro-products',
                'price' => 42000,
                'old_price' => null,
                'stock' => 0,
                'in_stock' => false,
                'short_description' => 'Packaged agro product supply option for food value-chain buyers.',
                'description' => 'A packaged agro product option for buyers that need coordinated sourcing and delivery.',
                'image' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=900&q=80',
            ],
        ];
    }
}
