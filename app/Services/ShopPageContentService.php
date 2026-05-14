<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SiteSettingRepository;

/**
 * Assembles CMS-managed content and product data for public shop pages.
 */
class ShopPageContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private ProductRepository $products,
        private SiteSettingRepository $settings
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function indexContent(?string $categorySlug = null): array
    {
        $page = $this->publishedPage('shop');
        $sections = $this->sectionsForPage($page);
        $categories = $this->categories();
        $products = $this->products();

        if ($categorySlug !== null) {
            $products = array_values(array_filter(
                $products,
                static fn (array $product): bool => $product['category_slug'] === $categorySlug
            ));
        }

        $category = $categorySlug === null ? null : $this->categoryBySlug($categorySlug);
        $title = $category['name'] ?? ($page['meta_title'] ?: $page['title']);

        return [
            'title' => (string) $title,
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'listing' => $sections['listing'] ?? [],
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $categorySlug,
            'seo' => $categorySlug === null
                ? $this->pageSeo($page, '/shop')
                : $this->categorySeo($category ?? ['name' => 'Shop Category', 'slug' => $categorySlug]),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function productContent(string $slug): ?array
    {
        $page = $this->publishedPage('shop');
        $sections = $this->sectionsForPage($page);
        $product = $this->productBySlug($slug);

        if ($product === null) {
            return null;
        }

        $related = array_values(array_filter(
            $this->products(),
            static fn (array $item): bool => $item['slug'] !== $slug
                && $item['category_slug'] === $product['category_slug']
        ));

        return [
            'title' => $product['name'],
            'page' => $page,
            'product' => $product,
            'detail' => $sections['product_detail'] ?? [],
            'relatedProducts' => $related,
            'seo' => $this->productSeo($product, $slug),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function cartContent(array $sessionCart): array
    {
        $page = $this->publishedPage('shop-cart');
        $sections = $this->sectionsForPage($page);
        $items = $this->cartItems($sessionCart);

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'cart' => $sections['cart'] ?? [],
            'items' => $items,
            'totals' => $this->totals($items),
            'seo' => $this->pageSeo($page, '/shop/cart'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function checkoutContent(array $sessionCart): array
    {
        $page = $this->publishedPage('shop-checkout');
        $sections = $this->sectionsForPage($page);
        $items = $this->cartItems($sessionCart);

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'page' => $page,
            'hero' => $sections['hero'] ?? [],
            'checkout' => $sections['checkout'] ?? [],
            'items' => $items,
            'totals' => $this->totals($items),
            'seo' => $this->pageSeo($page, '/shop/checkout'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function cartItems(array $sessionCart): array
    {
        $items = [];

        foreach ($sessionCart as $slug => $quantity) {
            $product = $this->productBySlug((string) $slug);

            if ($product === null) {
                continue;
            }

            $product['quantity'] = (int) $quantity;
            $product['subtotal'] = $product['quantity'] * $product['price'];
            $items[] = $product;
        }

        return $items;
    }

    /**
     * @return array<string, int|float>
     */
    public function totals(array $items): array
    {
        $subtotal = array_sum(array_map(static fn (array $item): float => (float) $item['subtotal'], $items));
        $shipping = $subtotal > 0 ? 5000 : 0;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function productBySlug(string $slug): ?array
    {
        foreach ($this->products() as $product) {
            if ($product['slug'] === $slug) {
                return $product;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function categories(): array
    {
        return array_map(static fn (array $row): array => [
            'id' => (int) ($row['id'] ?? 0),
            'slug' => (string) ($row['slug'] ?? ''),
            'name' => (string) ($row['name'] ?? ''),
        ], $this->products->activeCategories());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function products(): array
    {
        return array_map(function (array $row): array {
            $stock = (int) ($row['quantity_in_stock'] ?? 0);
            $price = (float) ($row['discount_price'] ?: $row['price']);

            return [
                'id' => (int) $row['id'],
                'slug' => (string) $row['slug'],
                'name' => (string) $row['name'],
                'sku' => (string) $row['sku'],
                'category' => (string) ($row['category_name'] ?? 'Products'),
                'category_slug' => (string) ($row['category_slug'] ?? 'products'),
                'price' => $price,
                'old_price' => !empty($row['discount_price']) ? (float) $row['price'] : null,
                'stock' => $stock,
                'in_stock' => $stock > 0,
                'short_description' => (string) ($row['short_description'] ?? ''),
                'description' => (string) ($row['description'] ?? ''),
                'image' => (string) ($row['display_image'] ?: $row['featured_image'] ?: ''),
                'gallery' => $this->galleryForProduct(
                    (int) $row['id'],
                    (string) ($row['featured_image'] ?? ''),
                    (string) ($row['name'] ?? '')
                ),
                'meta_title' => (string) ($row['meta_title'] ?? ''),
                'meta_description' => (string) ($row['meta_description'] ?? ''),
            ];
        }, $this->products->activeForShop());
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function galleryForProduct(int $productId, string $featuredImage, string $productName): array
    {
        $gallery = [];
        $seen = [];

        foreach ($this->products->imagesForProduct($productId) as $image) {
            $path = trim((string) ($image['path'] ?? ''));
            if ($path === '' || isset($seen[$path])) {
                continue;
            }

            $seen[$path] = true;
            $gallery[] = [
                'path' => $path,
                'alt_text' => (string) ($image['alt_text'] ?: $productName),
            ];
        }

        if ($gallery === [] && $featuredImage !== '') {
            $gallery[] = [
                'path' => $featuredImage,
                'alt_text' => $productName,
            ];
        }

        return $gallery;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function categoryBySlug(string $slug): ?array
    {
        foreach ($this->categories() as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function publishedPage(string $slug): array
    {
        $page = $this->pages->findPublishedBySlug($slug);
        if ($page === null) {
            throw new \RuntimeException('The published shop page content is missing for slug "' . $slug . '". Run the database seeder or publish the page.');
        }

        return $page;
    }

    /**
     * @param array<string, mixed> $page
     * @return array<string, array<string, mixed>>
     */
    private function sectionsForPage(array $page): array
    {
        $sections = [];

        foreach ($this->sections->forPage((int) $page['id']) as $row) {
            $body = $this->decodeBody((string) ($row['body'] ?? ''));
            $sections[(string) $row['section_key']] = array_merge($body, [
                'key' => (string) $row['section_key'],
                'heading' => (string) ($row['heading'] ?? ''),
            ]);
        }

        return $sections;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeBody(string $body): array
    {
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        return is_array($decoded) ? $decoded : ['text' => $body];
    }

    /**
     * @param array<string, mixed> $page
     * @return array<string, mixed>
     */
    private function pageSeo(array $page, string $path): array
    {
        $baseUrl = $this->baseUrl();

        return [
            'title' => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords' => (string) ($page['meta_keywords'] ?: ''),
            'canonical' => $baseUrl . $path,
            'image' => (string) ($page['featured_image'] ?? ''),
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'Shop' => $baseUrl . '/shop',
                ]),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $category
     * @return array<string, mixed>
     */
    private function categorySeo(array $category): array
    {
        $baseUrl = $this->baseUrl();
        $name = (string) ($category['name'] ?? 'Shop Category');
        $slug = (string) ($category['slug'] ?? '');

        return [
            'title' => $name . ' | Desnky Shop',
            'description' => 'Browse ' . strtolower($name) . ' available through Desnky Global Resources Ltd.',
            'keywords' => strtolower($name) . ', Desnky shop, Nigeria business supplies',
            'canonical' => $baseUrl . '/shop/category/' . $slug,
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'Shop' => $baseUrl . '/shop',
                    $name => $baseUrl . '/shop/category/' . $slug,
                ]),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $product
     * @return array<string, mixed>
     */
    private function productSeo(array $product, string $slug): array
    {
        $baseUrl = $this->baseUrl();

        return [
            'title' => ($product['meta_title'] ?: $product['name'] . ' | Desnky Shop'),
            'description' => ($product['meta_description'] ?: $product['short_description']),
            'canonical' => $baseUrl . '/shop/product/' . $slug,
            'image' => $product['image'],
            'type' => 'product',
            'keywords' => $product['name'] . ', ' . $product['category'] . ', Desnky shop Nigeria',
            'schema' => [
                SeoHelper::productSchema($product),
                SeoHelper::breadcrumbSchema([
                    'Home' => $baseUrl . '/',
                    'Shop' => $baseUrl . '/shop',
                    $product['category'] => $baseUrl . '/shop/category/' . $product['category_slug'],
                    $product['name'] => $baseUrl . '/shop/product/' . $slug,
                ]),
            ],
        ];
    }

    private function baseUrl(): string
    {
        return rtrim((string) ($this->settings->publicSettings()['site.url'] ?? 'https://www.desnkygroup.com'), '/');
    }
}
