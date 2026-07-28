<?php

namespace App\Services;

use App\Helpers\SeoHelper;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceHeroRepository;
use App\Repositories\SiteSettingRepository;
use App\Repositories\TrustedClientRepository;

/**
 * Assembles CMS-managed content required by public service pages.
 */
class ServicePageContentService
{
    public function __construct(
        private PageRepository $pages,
        private PageSectionRepository $sections,
        private ServiceRepository $services,
        private SiteSettingRepository $settings,
        private ?ProjectRepository $projects = null,
        private ?TrustedClientRepository $trustedClients = null,
        private ?ServiceDocumentationService $documentation = null,
        private ?ServiceHeroRepository $serviceHeroes = null
    ) {
    }

    /** @return array<string, mixed> */
    public function indexContent(): array
    {
        $page     = $this->publishedServicesPage();
        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));

        return [
            'title'    => (string) ($page['meta_title'] ?: $page['title']),
            'page'     => $page,
            'hero'     => $sections['hero'] ?? [],
            'listing'  => $sections['listing'] ?? [],
            'services' => $this->publishedServices(),
            'seo'      => $this->pageSeo($page),
        ];
    }

    /** @return array<string, mixed>|null */
    public function showContent(string $slug): ?array
    {
        $page     = $this->publishedServicesPage();
        $sections = $this->indexSections($this->sections->forPage((int) $page['id']));
        $service  = $this->services->findPublishedBySlug($slug);

        if ($service === null) {
            return null;
        }

        $service = $this->normalizeService($service);
        $documentation = $this->documentation()->forSlug($slug);

        if ($documentation !== null) {
            $service['documentation'] = $documentation;
            $service['title'] = (string) ($documentation['title'] ?: $service['title']);
            $service['summary'] = (string) ($documentation['intro_heading'] ?: $service['summary']);
            $service['faqs'] = \is_array($documentation['faqs'] ?? null) && $documentation['faqs'] !== []
                ? $documentation['faqs']
                : $service['faqs'];
        }

        $related = array_values(array_filter(
            $this->publishedServices(),
            static fn (array $item): bool => $item['slug'] !== $slug
        ));
        $managedHeroSlides = $this->managedHeroSlides($service);

        return [
            'title'          => (string) (($documentation['seo']['title'] ?? '') ?: $service['title']),
            'page'           => $page,
            'service'        => $service,
            'heroSlides'     => $managedHeroSlides ?? $this->fallbackHeroSlides($service),
            'heroManaged'    => $managedHeroSlides !== null,
            'documentation'  => $documentation,
            'detail'         => $sections['detail'] ?? [],
            'relatedServices'=> $related,
            'featuredProjects'=> $this->projectsForService($slug),
            'trustedClients' => $this->clientsForService($slug),
            'seo'            => $this->serviceSeo($service, $slug, $documentation),
        ];
    }

    /** @return array<string, mixed> */
    private function publishedServicesPage(): array
    {
        $page = $this->pages->findPublishedBySlug('services');
        if ($page === null) {
            throw new \RuntimeException('The published services page content is missing. Run the database seeder or publish a page with slug "services".');
        }

        return $page;
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
        return array_map($this->normalizeService(...), $this->services->published());
    }

    /** @return array<string, mixed> */
    private function normalizeService(array $row): array
    {
        $structured = $this->decodeBody((string) ($row['content'] ?? ''));

        return [
            'id'              => (int) ($row['id'] ?? 0),
            'slug'            => (string) $row['slug'],
            'title'           => (string) $row['title'],
            'icon'            => (string) ($row['icon'] ?? 'SR'),
            'category'        => (string) ($row['category'] ?? ''),
            'summary'         => (string) ($row['summary'] ?? ''),
            'image'           => (string) ($row['featured_image'] ?? ''),
            'overview'        => (string) ($structured['overview'] ?? $row['content'] ?? ''),
            'features'        => $this->stringList($structured['features'] ?? []),
            'process'         => $this->stringList($structured['process'] ?? []),
            'benefits'        => $this->stringList($structured['benefits'] ?? []),
            'key_benefits'    => $this->stringList($structured['key_benefits'] ?? $structured['benefits'] ?? []),
            'faqs'            => \is_array($structured['faqs'] ?? null) ? $structured['faqs'] : [],
            'seo_title'       => (string) ($row['meta_title'] ?? $row['title']),
            'seo_description' => (string) ($row['meta_description'] ?? $row['summary'] ?? ''),
            'seo_keywords'    => (string) ($row['meta_keywords'] ?? ''),
            'canonical_url'   => (string) ($row['canonical_url'] ?? ''),
            'og_image'        => (string) ($row['og_image'] ?? ''),
        ];
    }

    private function managedHeroSlides(array $service): ?array
    {
        if ($this->serviceHeroes !== null && (int) $service['id'] > 0) {
            if ($this->serviceHeroes->forService((int) $service['id']) !== []) {
                return $this->serviceHeroes->activeForService((int) $service['id']);
            }
        }

        return null;
    }

    private function fallbackHeroSlides(array $service): array
    {
        return [[
            'heading' => (string) $service['title'],
            'subheading' => (string) $service['summary'],
            'description' => '',
            'media_type' => 'image',
            'background_media' => (string) $service['image'],
            'primary_cta_label' => 'Discuss your requirement',
            'primary_cta_url' => '/contact?service=' . rawurlencode((string) $service['slug']) . '#contact-form',
            'secondary_cta_label' => 'Explore capabilities',
            'secondary_cta_url' => '#service-content',
        ]];
    }

    /**
     * @param mixed $value
     * @return array<int, string>
     */
    private function stringList(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $item): string => trim((string) $item),
            $value
        )));
    }

    private function documentation(): ServiceDocumentationService
    {
        if ($this->documentation !== null) {
            return $this->documentation;
        }

        return $this->documentation = new ServiceDocumentationService();
    }

    /** @return array<int, array<string, mixed>> */
    private function projectsForService(string $serviceSlug): array
    {
        if ($this->projects === null) {
            return [];
        }

        $all = $this->projects->published();

        // Filter projects whose category matches the service slug or title (loose match).
        $filtered = array_filter($all, static fn (array $p): bool =>
            str_contains(strtolower((string) ($p['category'] ?? '')), strtolower($serviceSlug))
        );

        // Fall back to latest 3 published projects when no category match found.
        $pool = count($filtered) > 0 ? array_values($filtered) : $all;

        return \array_slice(array_map(
            static fn (array $p): array => [
                'slug'     => (string) ($p['slug'] ?? ''),
                'title'    => (string) ($p['title'] ?? ''),
                'summary'  => (string) ($p['summary'] ?? ''),
                'category' => (string) ($p['category'] ?? ''),
                'image'    => (string) ($p['featured_image'] ?? ''),
            ],
            $pool
        ), 0, 3);
    }

    /** @return array<int, array<string, mixed>> */
    private function clientsForService(string $serviceSlug): array
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
            $this->trustedClients->active($serviceSlug)
        );
    }

    /** @return array<string, mixed> */
    private function pageSeo(array $page): array
    {
        $settings = $this->settings->publicSettings();
        $base     = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');

        return [
            'title'       => (string) ($page['meta_title'] ?: $page['title']),
            'description' => (string) ($page['meta_description'] ?: $page['excerpt'] ?: ''),
            'keywords'    => (string) ($page['meta_keywords'] ?: ''),
            'canonical'   => "{$base}/services",
            'image'       => (string) ($page['featured_image'] ?? ''),
            'schema'      => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home'     => "{$base}/",
                    'Services' => "{$base}/services",
                ]),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function serviceSeo(array $service, string $slug, ?array $documentation = null): array
    {
        $settings = $this->settings->publicSettings();
        $base     = rtrim((string) ($settings['site.url'] ?? 'https://www.desnkygroup.com'), '/');
        $docSeo   = \is_array($documentation['seo'] ?? null) ? $documentation['seo'] : [];
        $docKeywords = \is_array($docSeo['supporting_keywords'] ?? null)
            ? $docSeo['supporting_keywords']
            : [];
        $keywordParts = array_filter(array_merge(
            [(string) ($docSeo['primary_keyword'] ?? '')],
            array_map('strval', $docKeywords)
        ));
        $faqPairs = [];

        if (\is_array($documentation['faqs'] ?? null)) {
            foreach ($documentation['faqs'] as $faq) {
                if (!\is_array($faq) || empty($faq['question']) || empty($faq['answer'])) {
                    continue;
                }

                $faqPairs[(string) $faq['question']] = (string) $faq['answer'];
            }
        }

        if ($faqPairs === []) {
            $faqPairs = [
                "Does Desnky Global provide {$service['title']} in Nigeria?" => "Yes. Desnky Global Resources Ltd supports Nigerian organizations with {$service['title']} and related business services.",
                'How can I request this service?' => 'Use the contact form or request a quote so the team can review your requirement and respond with next steps.',
            ];
        }

        return [
            'title'       => (string) (($docSeo['title'] ?? '') ?: $service['seo_title']),
            'description' => (string) (($docSeo['description'] ?? '') ?: $service['seo_description']),
            'keywords'    => $keywordParts !== []
                ? implode(', ', $keywordParts)
                : ($service['seo_keywords'] ?: "{$service['title']}, services Nigeria, Desnky Global"),
            'canonical'   => $service['canonical_url'] ?: "{$base}/services/{$slug}",
            'image'       => $service['og_image'] ?: $service['image'],
            'schema'      => [
                SeoHelper::serviceSchema($service),
                SeoHelper::breadcrumbSchema([
                    'Home'           => "{$base}/",
                    'Services'       => "{$base}/services",
                    $service['title']=> "{$base}/services/{$slug}",
                ]),
                SeoHelper::faqSchema($faqPairs),
            ],
        ];
    }
}
