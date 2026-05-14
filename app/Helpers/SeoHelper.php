<?php

namespace App\Helpers;

/**
 * Renders page-level SEO, social and schema metadata.
 */
class SeoHelper
{
    /**
     * Render metadata tags for the frontend layout.
     *
     * @param array $seo SEO data.
     * @return string
     */
    public static function render(array $seo = []): string
    {
        $title = self::value($seo, 'title', 'Desnky Global Resources Ltd');
        $description = self::value(
            $seo,
            'description',
            'Integrated energy, engineering, procurement, safety, ICT and agro solutions in Nigeria.'
        );
        $canonical = self::value($seo, 'canonical', self::currentUrl());
        $image = self::value(
            $seo,
            'image',
            'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80'
        );
        $type = self::value($seo, 'type', 'website');
        $schema = $seo['schema'] ?? self::organizationSchema();

        $html = [
            '<title>' . self::escape($title) . '</title>',
            '<meta name="description" content="' . self::escape($description) . '">',
            '<link rel="canonical" href="' . self::escape($canonical) . '">',
            '<meta property="og:title" content="' . self::escape($title) . '">',
            '<meta property="og:description" content="' . self::escape($description) . '">',
            '<meta property="og:type" content="' . self::escape($type) . '">',
            '<meta property="og:url" content="' . self::escape($canonical) . '">',
            '<meta property="og:image" content="' . self::escape($image) . '">',
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:title" content="' . self::escape($title) . '">',
            '<meta name="twitter:description" content="' . self::escape($description) . '">',
            '<meta name="twitter:image" content="' . self::escape($image) . '">',
            '<script type="application/ld+json">' . self::schema($schema) . '</script>',
        ];

        return implode("\n    ", $html);
    }

    /**
     * Build default organization schema.
     *
     * @return array
     */
    public static function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Desnky Global Resources Ltd',
            'url' => self::baseUrl(),
            'email' => 'info@desnkygroup.com',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Lagos',
                'addressCountry' => 'NG',
            ],
            'areaServed' => 'Nigeria',
            'knowsAbout' => [
                'Engineering services',
                'Energy solutions',
                'Procurement',
                'HSE and safety',
                'ICT solutions',
                'Agro products and food processing',
            ],
        ];
    }

    /**
     * Build service schema.
     *
     * @param array $service Service data.
     * @return array
     */
    public static function serviceSchema(array $service): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service['title'] ?? 'Desnky Global Resources Service',
            'description' => $service['summary'] ?? '',
            'provider' => [
                '@type' => 'Organization',
                'name' => 'Desnky Global Resources Ltd',
                'url' => self::baseUrl(),
            ],
            'areaServed' => 'Nigeria',
            'url' => self::baseUrl() . '/services/' . ($service['slug'] ?? ''),
        ];
    }

    /**
     * Build product schema.
     *
     * @param array $product Product data.
     * @return array
     */
    public static function productSchema(array $product): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'] ?? '',
            'description' => $product['description'] ?? '',
            'sku' => $product['sku'] ?? '',
            'image' => $product['image'] ?? '',
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'NGN',
                'price' => (string) ($product['price'] ?? '0'),
                'availability' => !empty($product['in_stock'])
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
            ],
        ];
    }

    /**
     * Build breadcrumb schema.
     *
     * @param array $items Label => URL pairs.
     * @return array
     */
    public static function breadcrumbSchema(array $items): array
    {
        $position = 1;
        $elements = [];

        foreach ($items as $name => $url) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $name,
                'item' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    private static function value(array $data, string $key, string $default): string
    {
        return isset($data[$key]) && is_string($data[$key]) && $data[$key] !== '' ? $data[$key] : $default;
    }

    private static function schema(array $schema): string
    {
        return (string) json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function currentUrl(): string
    {
        return rtrim(self::baseUrl(), '/') . ($_SERVER['REQUEST_URI'] ?? '/');
    }

    private static function baseUrl(): string
    {
        return rtrim($_ENV['APP_URL'] ?? 'https://www.desnkygroup.com', '/');
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
