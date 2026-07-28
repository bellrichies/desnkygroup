<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\BlogPostRepository;
use App\Repositories\HeroSliderRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSettingRepository;
use App\Repositories\TrustedClientRepository;

/**
 * Assembles CMS-managed content required by the public home page.
 */
class HomeContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private ServiceRepository $services,
        private ProjectRepository $projects,
        private SiteSettingRepository $settings,
        private ?HeroSliderRepository $heroSliders = null,
        private ?TrustedClientRepository $trustedClients = null,
        private ?BlogPostRepository $blogPosts = null
    ) {
    }

    /** @return array<string, mixed> */
    public function getContent(): array
    {
        $page = $this->pages->findPublishedBySlug('home');
        if ($page === null) {
            throw new \RuntimeException('The published home page content is missing. Run the database seeder or publish a page with slug "home".');
        }

        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));
        $settings = $this->settings->publicSettings();

        return [
            'page'          => $page,
            'title'         => (string) ($page['meta_title'] ?: $page['title']),
            'hero'          => $sections['hero'] ?? [],
            'heroSlides'    => $this->activeHeroSlides(),
            'servicesIntro' => $sections['services_intro'] ?? [],
            'whyChooseUs'   => $sections['why_choose_us'] ?? [],
            'hseCommitment' => $sections['hse_commitment'] ?? [],
            'projectsIntro' => $sections['projects_intro'] ?? [],
            'clientsSection'=> $sections['clients'] ?? [],
            'stats'         => $sections['stats'] ?? [],
            'testimonials'  => $sections['testimonials']['items'] ?? [],
            'cta'           => $sections['cta'] ?? [],
            'services'      => $this->publishedServices(),
            'projects'      => \array_slice($this->publishedProjects(), 0, 3),
            'blogPosts'     => $this->recentBlogPosts(),
            'trustedClients'=> $this->activeTrustedClients(),
            'seo'           => $this->seoFromPage($page, $settings),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, array<string, mixed>>
     */
    private function indexSections(array $rows): array
    {
        $sections = [];

        foreach ($rows as $row) {
            $body = $this->decodeBody((string) ($row['body'] ?? ''));
            $sections[(string) $row['section_key']] = [
                ...$body,
                'key'     => (string) $row['section_key'],
                'heading' => (string) ($row['heading'] ?? ''),
            ];
        }

        return $sections;
    }

    /** @return array<string, mixed> */
    private function decodeBody(string $body): array
    {
        if ($body === '') {
            return [];
        }

        $decoded = json_decode($body, true);

        return \is_array($decoded) ? $decoded : ['text' => $body];
    }

    /** @return array<int, array<string, mixed>> */
    private function publishedServices(): array
    {
        return array_map(
            static fn (array $s): array => [
                'slug'    => (string) $s['slug'],
                'title'   => (string) $s['title'],
                'icon'    => (string) ($s['icon'] ?? 'SR'),
                'summary' => (string) ($s['summary'] ?? ''),
                'image'   => (string) ($s['featured_image'] ?? ''),
            ],
            $this->services->published()
        );
    }

    /** @return array<int, array<string, mixed>> */
    private function publishedProjects(): array
    {
        return array_map(
            static fn (array $p): array => [
                'slug'     => (string) ($p['slug'] ?? ''),
                'title'    => (string) ($p['title'] ?? ''),
                'summary'  => (string) ($p['summary'] ?? ''),
                'category' => (string) ($p['category'] ?? ''),
                'image'    => (string) ($p['featured_image'] ?? ''),
            ],
            $this->projects->published()
        );
    }

    /** @return array<int, array<string, mixed>> */
    private function recentBlogPosts(): array
    {
        if ($this->blogPosts === null) {
            return [];
        }

        try {
            return array_map(static function (array $post): array {
                $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($post['content'] ?? ''))) ?? '');
                $excerpt = trim((string) ($post['excerpt'] ?? ''));
                if ($excerpt === '' && $plainContent !== '') {
                    $excerpt = mb_strlen($plainContent) > 145
                        ? rtrim(mb_substr($plainContent, 0, 142)) . '...'
                        : $plainContent;
                }

                return [
                    'slug'          => (string) ($post['slug'] ?? ''),
                    'title'         => (string) ($post['title'] ?? ''),
                    'excerpt'       => $excerpt,
                    'image'         => (string) ($post['featured_image'] ?? ''),
                    'image_alt'     => (string) (($post['featured_image_alt'] ?? '') ?: ($post['title'] ?? '')),
                    'category_name' => (string) ($post['category_name'] ?? ''),
                    'published_at'  => (string) ($post['publish_date'] ?? $post['published_at'] ?? $post['created_at'] ?? ''),
                    'reading_time'  => max(1, (int) ceil(str_word_count($plainContent) / 200)),
                ];
            }, $this->blogPosts->recent(3));
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function activeTrustedClients(): array
    {
        if ($this->trustedClients === null) {
            return [];
        }

        return array_map(
            static fn (array $c): array => [
                'name'        => (string) $c['name'],
                'logo'        => (string) ($c['logo'] ?? ''),
                'website_url' => (string) ($c['website_url'] ?? ''),
            ],
            $this->trustedClients->active()
        );
    }

    /** @return array<int, array<string, mixed>> */
    private function activeHeroSlides(): array
    {
        if ($this->heroSliders === null) {
            return [];
        }

        try {
            return array_map(
                static fn (array $s): array => [
                    'id' => (int) $s['id'],
                    'heading' => (string) $s['heading'],
                    'caption' => (string) ($s['caption'] ?? ''),
                    'background_image' => (string) $s['background_image'],
                    'primary_cta_label' => (string) ($s['primary_cta_label'] ?? ''),
                    'primary_cta_url' => (string) ($s['primary_cta_url'] ?? ''),
                    'secondary_cta_label' => (string) ($s['secondary_cta_label'] ?? ''),
                    'secondary_cta_url' => (string) ($s['secondary_cta_url'] ?? ''),
                    'sort_order' => (int) ($s['sort_order'] ?? 0),
                ],
                $this->heroSliders->active()
            );
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * @param array<string, mixed> $page
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    private function seoFromPage(array $page, array $settings): array
    {
        return [
            'title'       => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords'    => (string) ($page['meta_keywords'] ?: ''),
            'canonical'   => (string) ($settings['site.url'] ?? 'https://www.desnkygroup.com/'),
            'image'       => (string) ($page['featured_image'] ?? ''),
            'schema'      => [
                SeoHelper::organizationSchema(),
                SeoHelper::localBusinessSchema(),
                SeoHelper::websiteSchema(),
            ],
        ];
    }
}
