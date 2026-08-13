<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SiteSettingRepository;
use App\Services\ShopPageContentService;
use App\Support\DatabaseFactory;

/**
 * Public shop browsing controller.
 */
class ShopController extends BaseController
{
    public function __construct(private ?ShopPageContentService $shopContent = null)
    {
    }

    public function index(): string
    {
        $content = $this->content()->indexContent();

        return $this->view('frontend/pages/shop/index', [
            'title' => $content['title'],
            'active' => 'shop',
            'csrf_token' => $this->csrf(),
        ] + $content);
    }

    public function category(string $slug): string
    {
        $content = $this->content()->indexContent($slug);

        return $this->view('frontend/pages/shop/index', [
            'title' => $content['title'],
            'active' => 'shop',
            'csrf_token' => $this->csrf(),
        ] + $content);
    }

    public function show(string $slug): string
    {
        $content = $this->content()->productContent($slug);

        if ($content === null) {
            http_response_code(404);

            return $this->view('frontend/pages/show', [
                'title' => 'Product Not Found',
                'content' => '<p>The requested product could not be found.</p>',
                'active' => 'shop',
                'seo' => [
                    'title' => 'Product Not Found | Desnky Shop',
                    'description' => 'The requested Desnky Global Resources shop product could not be found.',
                    'robots' => 'noindex, follow',
                ],
            ]);
        }

        return $this->view('frontend/pages/shop/product', [
            'title' => $content['title'],
            'active' => 'shop',
            'csrf_token' => $this->csrf(),
        ] + $content);
    }

    private function content(): ShopPageContentService
    {
        if ($this->shopContent !== null) {
            return $this->shopContent;
        }

        return $this->shopContent = self::contentService();
    }

    public static function contentService(): ShopPageContentService
    {
        $connection = DatabaseFactory::make();

        return new ShopPageContentService(
            new PageRepository($connection),
            new PageSectionRepository($connection),
            new ProductRepository($connection),
            new SiteSettingRepository($connection)
        );
    }
}
