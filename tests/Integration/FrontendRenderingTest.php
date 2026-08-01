<?php

namespace Tests\Integration;

use App\Config;
use App\Controllers\Frontend\CartController;
use App\Controllers\Frontend\CheckoutController;
use App\Controllers\Frontend\ContactController;
use App\Controllers\Frontend\HomeController;
use App\Controllers\Frontend\PageController;
use App\Controllers\Frontend\ServiceController;
use App\Controllers\Frontend\ShopController;
use PHPUnit\Framework\TestCase;

class FrontendRenderingTest extends TestCase
{
    /**
     * @var array<string, mixed>|null
     */
    private static ?array $databaseConfig = null;

    protected function setUp(): void
    {
        self::$databaseConfig ??= (array) Config::get('database.connections.mysql');
        Config::set('database.connections.mysql', self::$databaseConfig);
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        Config::set('database.connections.mysql', self::$databaseConfig);
        $_SESSION = [];
    }

    public function testHomepageRendersSeoSchemaAndSingleH1(): void
    {
        $html = (new HomeController())->index();

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('<meta name="viewport"', $html);
        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertStringContainsString('Integrated Energy, Engineering, Procurement', $html);
        $this->assertMatchesRegularExpression(
            '~href="/assets/css/main\.css\?v=\d+"~',
            $html
        );
        $this->assertMatchesRegularExpression(
            '~src="/assets/js/ajax-handler\.js\?v=\d+"~',
            $html
        );
        $this->assertImagesHaveAltText($html);
    }

    public function testContactPageRendersAccessibleFormAndCsrfToken(): void
    {
        $_SERVER['REQUEST_URI'] = '/contact';

        $html = (new ContactController())->show();

        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString('for="full_name"', $html);
        $this->assertStringContainsString('ContactPoint', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testServicesIndexRendersDatabaseBackedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/services';

        $html = (new ServiceController())->index();

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Explore our core service areas', $html);
        $this->assertStringContainsString('Engineering Services', $html);
        $this->assertStringContainsString('application/ld+json', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testAboutPageRendersDatabaseBackedSections(): void
    {
        $_SERVER['REQUEST_URI'] = '/about';

        $html = (new PageController())->about();

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Built for practical delivery across Nigeria', $html);
        $this->assertStringContainsString('A multi-sector partner focused on dependable execution', $html);
        $this->assertStringContainsString('How we work with clients', $html);
        $this->assertStringContainsString('Desmond Nkwocha', $html);
        $this->assertStringContainsString('Project Coordinator', $html);
        $this->assertStringContainsString('BreadcrumbList', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testHsePolicyPageRendersDatabaseBackedSections(): void
    {
        $_SERVER['REQUEST_URI'] = '/hse-policy';

        $html = (new PageController())->hse();

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Corporate Health, Safety and Environment (HSE) Policy', $html);
        $this->assertStringContainsString('Management shall ensure the following', $html);
        $this->assertStringContainsString('zero-compromise approach toward health, safety, and environmental standards', $html);
        $this->assertStringContainsString('Policy communication and accessibility', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testServiceDetailRendersStructuredDatabaseContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/services/engineering';

        $html = (new ServiceController())->show('engineering');

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Electrical installation support', $html);
        $this->assertStringContainsString('Our process', $html);
        $this->assertStringContainsString('Related services', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testProjectsPageRendersFilterButtonsAndStructuredMetadata(): void
    {
        $_SERVER['REQUEST_URI'] = '/projects';

        $html = (new PageController())->projects();

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('A representative gallery', $html);
        $this->assertStringContainsString('data-category="Engineering"', $html);
        $this->assertStringContainsString('BreadcrumbList', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testProjectDetailRendersStructuredDatabaseContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/projects/industrial-electrical-support';

        $html = (new PageController())->project('industrial-electrical-support');

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Installation support planning', $html);
        $this->assertStringContainsString('Project summary', $html);
        $this->assertStringContainsString('CreativeWork', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testShopIndexRendersDatabaseBackedProducts(): void
    {
        $_SERVER['REQUEST_URI'] = '/shop';

        $html = (new ShopController())->index();

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Industrial Safety Helmet', $html);
        $this->assertStringContainsString('Safety Equipment', $html);
        $this->assertStringContainsString('BreadcrumbList', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testShopCategoryRendersFilteredProducts(): void
    {
        $_SERVER['REQUEST_URI'] = '/shop/category/safety-equipment';

        $html = (new ShopController())->category('safety-equipment');

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Industrial Safety Helmet', $html);
        $this->assertStringNotContainsString('Business Network Router Kit', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testProductDetailRendersDatabaseBackedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/shop/product/industrial-safety-helmet';

        $html = (new ShopController())->show('industrial-safety-helmet');

        $this->assertSame(1, substr_count(strtolower($html), '<h1'));
        $this->assertStringContainsString('Product details', $html);
        $this->assertStringContainsString('HSE-HELMET-01', $html);
        $this->assertStringContainsString('Safety helmet with worksite protective gear', $html);
        $this->assertStringContainsString('Product', $html);
        $this->assertImagesHaveAltText($html);
    }

    public function testCartAndCheckoutRenderCmsBackedLabels(): void
    {
        $_SERVER['REQUEST_URI'] = '/shop/cart';
        $_SESSION['cart'] = ['industrial-safety-helmet' => 2];

        $cartHtml = (new CartController())->index();

        $this->assertSame(1, substr_count(strtolower($cartHtml), '<h1'));
        $this->assertStringContainsString('Cart Summary', $cartHtml);
        $this->assertStringContainsString('Industrial Safety Helmet', $cartHtml);

        $_SERVER['REQUEST_URI'] = '/shop/checkout';
        $checkoutHtml = (new CheckoutController())->index();

        $this->assertSame(1, substr_count(strtolower($checkoutHtml), '<h1'));
        $this->assertStringContainsString('Manual bank transfer', $checkoutHtml);
        $this->assertStringContainsString('Order Review', $checkoutHtml);
    }

    private function assertImagesHaveAltText(string $html): void
    {
        preg_match_all('/<img\b[^>]*>/i', $html, $matches);

        foreach ($matches[0] as $imageTag) {
            $this->assertMatchesRegularExpression('/\salt=(["\']).+?\1/i', $imageTag);
        }
    }
}
