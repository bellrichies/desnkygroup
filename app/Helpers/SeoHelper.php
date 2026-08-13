<?php

namespace App\Helpers;

use App\Config;

/**
 * Renders page-level SEO, social and schema metadata.
 */
class SeoHelper
{
    private string $title;
    private string $description;
    private string $keywords = '';
    private string $ogImage = '';
    private string $canonical = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $schemas = [];

    public function __construct()
    {
        $this->title = (string) Config::get('seo.default_title', 'Desnky Global Resources Ltd');
        $this->description = (string) Config::get('seo.default_description', '');
        $this->keywords = (string) Config::get('seo.default_keywords', '');
        $this->ogImage = (string) Config::get('seo.default_image', '');
        $this->canonical = self::currentUrl();
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function setOgImage(string $url): self
    {
        $this->ogImage = $url;

        return $this;
    }

    public function setCanonical(string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    /**
     * @param array<string, mixed> $schema
     */
    public function addSchema(array $schema): self
    {
        $this->schemas[] = $schema;

        return $this;
    }

    public function toHtml(): string
    {
        return self::render([
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'image' => $this->ogImage,
            'canonical' => $this->canonical,
            'schema' => $this->schemas,
        ]);
    }

    /**
     * Render metadata tags for the frontend layout.
     *
     * @param array $seo SEO data.
     * @return string
     */
    public static function render(array $seo = []): string
    {
        $title = self::value($seo, 'title', (string) Config::get('seo.default_title', 'Desnky Global Resources Ltd'));
        $description = self::value(
            $seo,
            'description',
            (string) Config::get('seo.default_description', '')
        );
        $keywords = self::value($seo, 'keywords', (string) Config::get('seo.default_keywords', ''));
        $canonical = self::absoluteUrl(self::value($seo, 'canonical', self::currentUrl()));
        $image = self::value(
            $seo,
            'image',
            (string) Config::get('seo.default_image', '')
        );
        $image = self::absoluteUrl($image !== '' ? $image : (string) Config::get('seo.default_image', ''));
        $type = self::value($seo, 'type', 'website');
        $schema = self::normalizeSchemas($seo['schema'] ?? [self::organizationSchema(), self::websiteSchema()]);
        $siteName = (string) Config::get('seo.site_name', 'Desnky Global Resources Ltd');

        $html = [
            '<title>' . self::escape($title) . '</title>',
            '<meta name="description" content="' . self::escape($description) . '">',
            '<meta name="keywords" content="' . self::escape($keywords) . '">',
            '<meta name="robots" content="' . self::escape($seo['robots'] ?? 'index, follow') . '">',
            '<link rel="canonical" href="' . self::escape($canonical) . '">',
            '<meta property="og:site_name" content="' . self::escape($siteName) . '">',
            '<meta property="og:title" content="' . self::escape($title) . '">',
            '<meta property="og:description" content="' . self::escape($description) . '">',
            '<meta property="og:type" content="' . self::escape($type) . '">',
            '<meta property="og:url" content="' . self::escape($canonical) . '">',
            '<meta property="og:image" content="' . self::escape($image) . '">',
            '<meta property="og:image:alt" content="' . self::escape((string) ($seo['image_alt'] ?? $title)) . '">',
            '<meta property="og:locale" content="en_NG">',
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:title" content="' . self::escape($title) . '">',
            '<meta name="twitter:description" content="' . self::escape($description) . '">',
            '<meta name="twitter:image" content="' . self::escape($image) . '">',
            '<meta name="twitter:image:alt" content="' . self::escape((string) ($seo['image_alt'] ?? $title)) . '">',
        ];

        $googleVerification = (string) Config::get('seo.analytics.google_site_verification', '');
        $bingVerification = (string) Config::get('seo.analytics.bing_site_verification', '');

        if ($googleVerification !== '') {
            $html[] = '<meta name="google-site-verification" content="' . self::escape($googleVerification) . '">';
        }

        if ($bingVerification !== '') {
            $html[] = '<meta name="msvalidate.01" content="' . self::escape($bingVerification) . '">';
        }

        $html[] = '<script type="application/ld+json">' . self::schema($schema) . '</script>';

        return implode("\n    ", $html);
    }

    /**
     * Build default organization schema.
     *
     * @return array
     */
    public static function organizationSchema(): array
    {
        $logo = self::logoImageObject();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => self::baseUrl() . '/#organization',
            'name' => 'Desnky Global Resources Ltd',
            'url' => self::baseUrl(),
            'logo' => $logo,
            'image' => $logo,
            'email' => (string) Config::get('seo.contact.email', 'info@desnkygroup.com'),
            'telephone' => (string) Config::get('seo.contact.phone', '+234'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => (string) Config::get('seo.address.street', 'Lagos, Nigeria'),
                'addressLocality' => (string) Config::get('seo.address.locality', 'Lagos'),
                'addressRegion' => (string) Config::get('seo.address.region', 'Lagos'),
                'addressCountry' => (string) Config::get('seo.address.country', 'NG'),
            ],
            'areaServed' => 'Nigeria',
            'sameAs' => (array) Config::get('seo.social_profiles', []),
            'knowsAbout' => [
                'Industrial engineering services in Nigeria',
                'Renewable energy and power solutions',
                'Procurement and supply chain management',
                'HSE consulting and workplace safety',
                'ICT infrastructure and business technology',
                'Agro-allied products and food processing',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => self::baseUrl() . '/#website',
            'name' => (string) Config::get('seo.site_name', 'Desnky Global Resources Ltd'),
            'url' => self::baseUrl(),
            'inLanguage' => 'en-NG',
            'publisher' => [
                '@id' => self::baseUrl() . '/#organization',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function localBusinessSchema(): array
    {
        return array_merge(self::organizationSchema(), [
            '@type' => 'LocalBusiness',
            'priceRange' => '$$',
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                    'opens' => '08:00',
                    'closes' => '17:00',
                ],
            ],
        ]);
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
            'serviceType' => $service['category'] ?? $service['title'] ?? 'Corporate services',
            'url' => self::baseUrl() . '/services/' . ($service['slug'] ?? ''),
            'image' => self::absoluteUrl((string) ($service['image'] ?? Config::get('seo.default_image', ''))),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function collectionPageSchema(string $name, string $description, string $url): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $name,
            'description' => $description,
            'url' => $url,
            'isPartOf' => self::websiteSchema(),
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
            'image' => self::absoluteUrl((string) ($product['image'] ?? Config::get('seo.default_image', ''))),
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => 'NGN',
                'price' => (string) ($product['price'] ?? '0'),
                'url' => self::baseUrl() . '/shop/product/' . ($product['slug'] ?? ''),
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

    /**
     * @param array<string, string> $questions Question => answer pairs.
     * @return array<string, mixed>
     */
    public static function faqSchema(array $questions): array
    {
        $items = [];

        foreach ($questions as $question => $answer) {
            $items[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function contactPointSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ContactPoint',
            'telephone' => (string) Config::get('seo.contact.phone', '+234'),
            'email' => (string) Config::get('seo.contact.email', 'info@desnkygroup.com'),
            'contactType' => 'customer service',
            'areaServed' => 'NG',
            'availableLanguage' => ['English'],
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

    /**
     * @param mixed $schema
     * @return array<string, mixed>
     */
    private static function normalizeSchemas($schema): array
    {
        if (!is_array($schema)) {
            return self::organizationSchema();
        }

        $isList = array_is_list($schema);

        if (!$isList) {
            return $schema;
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values($schema),
        ];
    }

    private static function currentUrl(): string
    {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);

        return rtrim(self::baseUrl(), '/') . ($path ?: '/');
    }

    private static function baseUrl(): string
    {
        return rtrim((string) Config::get('seo.base_url', 'https://www.desnkygroup.com'), '/');
    }

    /**
     * @return array<string, mixed>
     */
    private static function logoImageObject(): array
    {
        $logoUrl = self::absoluteUrl((string) Config::get('seo.logo', '/assets/images/logo.png'));

        return [
            '@type' => 'ImageObject',
            '@id' => self::baseUrl() . '/#logo',
            'url' => $logoUrl,
            'contentUrl' => $logoUrl,
            'caption' => (string) Config::get('seo.site_name', 'Desnky Global Resources Ltd') . ' logo',
            'width' => (int) Config::get('seo.logo_width', 500),
            'height' => (int) Config::get('seo.logo_height', 500),
        ];
    }

    private static function absoluteUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $url) === 1) {
            return $url;
        }

        return self::baseUrl() . '/' . ltrim($url, '/');
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
