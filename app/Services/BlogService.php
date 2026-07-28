<?php

namespace App\Services;

use App\Config;
use App\DTO\BlogPostPayload;
use App\Helpers\SeoHelper;
use App\Repositories\BlogAdRepository;
use App\Repositories\BlogPostRepository;
use App\Repositories\BlogTaxonomyRepository;
use InvalidArgumentException;

/**
 * Business logic for public and administrative blog workflows.
 */
class BlogService extends BaseService
{
    /**
     * @var array<int, string>
     */
    private const STATUSES = ['draft', 'scheduled', 'published', 'archived'];

    public function __construct(
        private BlogPostRepository $posts,
        private BlogTaxonomyRepository $taxonomy,
        private BlogAdRepository $ads,
        private BlogContentSanitizer $sanitizer,
        private ?CacheService $cache = null
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function statuses(): array
    {
        return self::STATUSES;
    }

    /**
     * @return array<string, mixed>
     */
    public function listingContext(array $filters = []): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $query = $this->cleanText((string) ($filters['q'] ?? ''), 100);
        $categorySlug = $this->cleanSlug((string) ($filters['category_slug'] ?? ''));
        $tagSlug = $this->cleanSlug((string) ($filters['tag_slug'] ?? ''));
        $isMainListing = $query === '' && $categorySlug === '' && $tagSlug === '';
        $featuredRows = $isMainListing ? $this->cachedFeatured() : [];
        if ($isMainListing && $featuredRows === []) {
            $featuredRows = $this->posts->recent(1);
        }
        $featuredRow = $featuredRows[0] ?? null;

        $listing = $this->posts->paginatePublished([
            'q' => $query,
            'category_slug' => $categorySlug,
            'tag_slug' => $tagSlug,
            'exclude_ids' => $featuredRow !== null ? [(int) $featuredRow['id']] : [],
        ], $page, 10);

        $contextTitle = 'Insights';
        $contextDescription = 'Practical updates, procurement guidance, engineering notes, HSE awareness and business operations insight from Desnky Global Resources Ltd.';
        $activeCategory = null;
        $activeTag = null;

        if ($categorySlug !== '') {
            $activeCategory = $this->taxonomy->findCategoryBySlug($categorySlug);
            $contextTitle = $activeCategory['name'] ?? 'Category';
            $contextDescription = (string) ($activeCategory['description'] ?? $contextDescription);
        }

        if ($tagSlug !== '') {
            $activeTag = $this->taxonomy->findTagBySlug($tagSlug);
            $contextTitle = '#' . (string) ($activeTag['name'] ?? 'Tag');
            $contextDescription = (string) ($activeTag['description'] ?? $contextDescription);
        }

        if ($query !== '') {
            $contextTitle = 'Search results';
            $contextDescription = 'Search results for "' . $query . '" in the Desnky insights library.';
        }

        $totalPages = max(1, (int) ceil($listing['total'] / $listing['per_page']));

        return [
            'title' => $contextTitle . ' | Blog',
            'heading' => $contextTitle,
            'description' => $contextDescription,
            'active' => 'blog',
            'posts' => array_map([$this, 'decoratePostSummary'], $listing['items']),
            'featuredPosts' => $page === 1 && $featuredRow !== null
                ? [$this->decoratePostSummary($featuredRow)]
                : [],
            'recentPosts' => array_map([$this, 'decoratePostSummary'], $this->cachedRecent()),
            'popularPosts' => array_map([$this, 'decoratePostSummary'], $this->cachedPopular()),
            'categories' => $this->cachedCategories(),
            'tags' => $this->cachedTags(),
            'activeCategory' => $activeCategory,
            'activeTag' => $activeTag,
            'filters' => ['q' => $query, 'category_slug' => $categorySlug, 'tag_slug' => $tagSlug],
            'pagination' => [
                'page' => $listing['page'],
                'per_page' => $listing['per_page'],
                'total' => $listing['total'],
                'total_pages' => $totalPages,
            ],
            'adPlacements' => $this->ads->enabledForContext('listing'),
            'seo' => $this->listingSeo($contextTitle, $contextDescription, $query),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function postContext(string $slug, bool $trackView = true): ?array
    {
        $slug = $this->cleanSlug($slug);
        $post = $this->posts->findPublishedBySlug($slug);

        if ($post === null) {
            return null;
        }

        $post = $this->withPostRelations($post);
        $post = $this->decoratePostSummary($post);
        $content = $this->contentWithToc((string) $post['content']);
        $post['content'] = $content['content'];
        $post['toc'] = $content['toc'];
        $wordCount = $this->wordCount((string) $post['content']);

        if ($trackView) {
            $this->trackView((int) $post['id']);
        }

        return [
            'title' => $post['seo_title'] ?: $post['title'],
            'active' => 'blog',
            'post' => $post,
            'relatedPosts' => array_map(
                [$this, 'decoratePostSummary'],
                $this->posts->related(
                    (int) $post['id'],
                    array_column($post['categories'], 'id'),
                    array_column($post['tags'], 'id'),
                    3
                )
            ),
            'adjacent' => $this->posts->adjacent($post),
            'recentPosts' => array_map([$this, 'decoratePostSummary'], $this->cachedRecent()),
            'popularPosts' => array_map([$this, 'decoratePostSummary'], $this->cachedPopular()),
            'adPlacements' => $this->filterAdsForWordCount($this->ads->enabledForContext('article'), $wordCount),
            'wordCount' => $wordCount,
            'seo' => $this->postSeo($post),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function previewContext(int $id): ?array
    {
        $post = $this->posts->findAdmin($id, true);
        if ($post === null) {
            return null;
        }

        $post = $this->withPostRelations($post);
        $post = $this->decoratePostSummary($post);
        $content = $this->contentWithToc((string) $post['content']);
        $post['content'] = $content['content'];
        $post['toc'] = $content['toc'];

        return [
            'title' => 'Preview: ' . $post['title'],
            'active' => 'blog',
            'post' => $post,
            'relatedPosts' => [],
            'adjacent' => ['previous' => null, 'next' => null],
            'recentPosts' => [],
            'popularPosts' => [],
            'adPlacements' => [],
            'isPreview' => true,
            'seo' => [
                'title' => 'Preview: ' . $post['title'],
                'description' => $post['excerpt'] ?? '',
                'canonical' => $this->permalink((string) $post['slug']),
                'robots' => 'noindex, nofollow',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adminPosts(array $filters = []): array
    {
        return $this->posts->adminAll([
            'q' => $this->cleanText((string) ($filters['q'] ?? ''), 100),
            'status' => in_array(($filters['status'] ?? ''), self::STATUSES, true) ? $filters['status'] : '',
            'category_id' => (int) ($filters['category_id'] ?? 0),
            'include_deleted' => !empty($filters['include_deleted']),
        ]);
    }

    public function findAdminPost(int $id, bool $withDeleted = false): ?array
    {
        $post = $this->posts->findAdmin($id, $withDeleted);

        return $post === null ? null : $this->withPostRelations($post);
    }

    public function createPost(array $input, ?int $adminUserId = null): int
    {
        $payload = $this->postPayload($input, null, $adminUserId);

        $postId = $this->posts->transaction(function () use ($payload, $adminUserId): int {
            $postId = $this->posts->create($payload->values);
            $this->posts->syncCategories($postId, $payload->categoryIds, $payload->primaryCategoryId);
            $this->posts->syncTags($postId, $payload->tagIds);
            $this->posts->recordRevision($postId, $adminUserId, $payload->values);

            return $postId;
        });

        $this->flushBlogCache();

        return $postId;
    }

    public function updatePost(int $id, array $input, ?int $adminUserId = null): bool
    {
        $existing = $this->posts->findAdmin($id, true);
        if ($existing === null) {
            throw new InvalidArgumentException('Blog post not found.');
        }

        $payload = $this->postPayload($input, $id, $adminUserId, $existing);

        $this->posts->transaction(function () use ($id, $existing, $payload, $adminUserId): void {
            $this->posts->update($id, $payload->values);
            if ((string) $existing['slug'] !== (string) $payload->values['slug']) {
                $this->posts->addSlugRedirect($id, (string) $existing['slug']);
            }
            $this->posts->syncCategories($id, $payload->categoryIds, $payload->primaryCategoryId);
            $this->posts->syncTags($id, $payload->tagIds);
            $this->posts->recordRevision($id, $adminUserId, $payload->values);
        });

        $this->flushBlogCache();

        return true;
    }

    public function deletePost(int $id): bool
    {
        $result = $this->posts->softDelete($id);
        $this->flushBlogCache();

        return $result;
    }

    public function restorePost(int $id): bool
    {
        $result = $this->posts->restore($id);
        $this->flushBlogCache();

        return $result;
    }

    public function updatePostStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid blog post status.');
        }

        $result = $this->posts->updateStatus($id, $status);
        $this->flushBlogCache();

        return $result;
    }

    /**
     * @param array<int, mixed> $ids
     */
    public function bulkStatus(array $ids, string $status): int
    {
        $updated = 0;

        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id > 0) {
                $this->updatePostStatus($id, $status);
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function categories(bool $includeInactive = true): array
    {
        return $this->taxonomy->categories($includeInactive);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function tags(): array
    {
        return $this->taxonomy->tags();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function authors(bool $activeOnly = false): array
    {
        return $this->taxonomy->authors($activeOnly);
    }

    public function createCategory(array $input): int
    {
        $id = $this->taxonomy->createCategory($this->categoryPayload($input));
        $this->flushBlogCache();

        return $id;
    }

    public function updateCategory(int $id, array $input): bool
    {
        $result = $this->taxonomy->updateCategory($id, $this->categoryPayload($input, $id));
        $this->flushBlogCache();

        return $result;
    }

    public function deleteCategory(int $id): bool
    {
        $result = $this->taxonomy->deleteCategory($id);
        $this->flushBlogCache();

        return $result;
    }

    public function createTag(array $input): int
    {
        $id = $this->taxonomy->createTag($this->tagPayload($input));
        $this->flushBlogCache();

        return $id;
    }

    public function updateTag(int $id, array $input): bool
    {
        $result = $this->taxonomy->updateTag($id, $this->tagPayload($input, $id));
        $this->flushBlogCache();

        return $result;
    }

    public function deleteTag(int $id): bool
    {
        $result = $this->taxonomy->deleteTag($id);
        $this->flushBlogCache();

        return $result;
    }

    public function createAuthor(array $input): int
    {
        $id = $this->taxonomy->createAuthor($this->authorPayload($input));
        $this->flushBlogCache();

        return $id;
    }

    public function updateAuthor(int $id, array $input): bool
    {
        $result = $this->taxonomy->updateAuthor($id, $this->authorPayload($input, $id));
        $this->flushBlogCache();

        return $result;
    }

    public function deleteAuthor(int $id): bool
    {
        $result = $this->taxonomy->deleteAuthor($id);
        $this->flushBlogCache();

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function adPlacements(): array
    {
        return $this->ads->all();
    }

    public function updateAdPlacement(int $id, array $input): bool
    {
        $placement = $this->ads->find($id);
        if ($placement === null) {
            throw new InvalidArgumentException('Advertisement placement not found.');
        }

        $result = $this->ads->update($id, [
            'label' => $this->cleanText((string) ($input['label'] ?? $placement['label']), 160),
            'description' => $this->cleanText((string) ($input['description'] ?? ''), 255),
            'display_context' => in_array(($input['display_context'] ?? 'blog'), ['blog', 'listing', 'article'], true)
                ? $input['display_context']
                : 'blog',
            'adsense_client' => $this->cleanText((string) ($input['adsense_client'] ?? ''), 80),
            'adsense_slot' => $this->cleanText((string) ($input['adsense_slot'] ?? ''), 80),
            'ad_format' => $this->cleanText((string) ($input['ad_format'] ?? 'auto'), 40),
            'reserved_height' => max(90, (int) ($input['reserved_height'] ?? 280)),
            'min_word_count' => max(0, (int) ($input['min_word_count'] ?? 0)),
            'sort_order' => (int) ($input['sort_order'] ?? 0),
            'is_enabled' => !empty($input['is_enabled']),
        ]);

        $this->flushBlogCache();

        return $result;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function feedItems(int $limit = 20): array
    {
        return array_map([$this, 'decoratePostSummary'], $this->posts->publishedForFeed($limit));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function sitemapPosts(): array
    {
        return $this->posts->publishedForSitemap();
    }

    /**
     * @return array<string, mixed>
     */
    public function stats(): array
    {
        return $this->posts->stats();
    }

    public function redirectForSlug(string $slug): ?string
    {
        $row = $this->posts->findRedirectBySlug($this->cleanSlug($slug));

        return $row ? '/blog/' . (string) $row['slug'] : null;
    }

    public function permalink(string $slug): string
    {
        return $this->baseUrl() . '/blog/' . $slug;
    }

    public function readingTime(string $content): int
    {
        return max(1, (int) ceil($this->wordCount($content) / 200));
    }

    private function postPayload(
        array $input,
        ?int $existingId = null,
        ?int $adminUserId = null,
        ?array $existing = null
    ): BlogPostPayload {
        $title = $this->cleanText((string) ($input['title'] ?? ''), 255);
        if ($title === '') {
            throw new InvalidArgumentException('Post title is required.');
        }

        $status = (string) ($input['status'] ?? 'draft');
        if (!in_array($status, self::STATUSES, true)) {
            $status = 'draft';
        }

        $content = $this->sanitizer->sanitize((string) ($input['content'] ?? ''));
        if (trim(strip_tags($content)) === '') {
            throw new InvalidArgumentException('Post content is required.');
        }

        $slug = $this->uniquePostSlug((string) ($input['slug'] ?? $title), $existingId);
        $publishedAt = $this->dateTimeOrNull((string) ($input['published_at'] ?? ''));
        $scheduledAt = $this->dateTimeOrNull((string) ($input['scheduled_at'] ?? ''));

        if ($status === 'published' && $publishedAt === null) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        if ($status === 'scheduled' && $scheduledAt === null) {
            throw new InvalidArgumentException('Scheduled posts require a scheduled publication date.');
        }

        if ($status !== 'scheduled') {
            $scheduledAt = null;
        }

        $canonicalUrl = $this->cleanText((string) ($input['canonical_url'] ?? ''), 255);
        if ($canonicalUrl !== '' && !filter_var($canonicalUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Canonical URL must be a valid absolute URL.');
        }

        $categoryIds = $this->intList($input['category_ids'] ?? []);
        $tagIds = $this->intList($input['tag_ids'] ?? []);
        $primaryCategoryId = (int) ($input['primary_category_id'] ?? 0);
        if ($primaryCategoryId <= 0 || !in_array($primaryCategoryId, $categoryIds, true)) {
            $primaryCategoryId = $categoryIds[0] ?? null;
        }

        $values = [
            'author_id' => (int) ($input['author_id'] ?? 0) ?: null,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $this->excerpt((string) ($input['excerpt'] ?? ''), $content),
            'content' => $content,
            'featured_image' => $this->cleanText((string) ($input['featured_image'] ?? ''), 255),
            'featured_image_alt' => $this->cleanText((string) ($input['featured_image_alt'] ?? ''), 255),
            'status' => $status,
            'is_featured' => !empty($input['is_featured']),
            'published_at' => $publishedAt,
            'scheduled_at' => $scheduledAt,
            'seo_title' => $this->cleanText((string) ($input['seo_title'] ?? ''), 255),
            'meta_description' => $this->cleanText((string) ($input['meta_description'] ?? ''), 180),
            'canonical_url' => $canonicalUrl,
            'og_image' => $this->cleanText((string) ($input['og_image'] ?? ''), 255),
            'robots_index' => !isset($input['robots_index']) || !empty($input['robots_index']),
            'created_by' => $existing['created_by'] ?? $adminUserId,
            'updated_by' => $adminUserId,
        ];

        return new BlogPostPayload($values, $categoryIds, $tagIds, $primaryCategoryId);
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryPayload(array $input, ?int $id = null): array
    {
        $name = $this->cleanText((string) ($input['name'] ?? ''), 160);
        if ($name === '') {
            throw new InvalidArgumentException('Category name is required.');
        }

        $slug = $this->uniqueTaxonomySlug(
            (string) ($input['slug'] ?? $name),
            fn (string $value, ?int $exclude): bool => $this->taxonomy->categorySlugExists($value, $exclude),
            $id
        );

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $this->cleanText((string) ($input['description'] ?? ''), 500),
            'seo_title' => $this->cleanText((string) ($input['seo_title'] ?? ''), 255),
            'meta_description' => $this->cleanText((string) ($input['meta_description'] ?? ''), 180),
            'is_active' => !empty($input['is_active']),
            'sort_order' => (int) ($input['sort_order'] ?? 0),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function tagPayload(array $input, ?int $id = null): array
    {
        $name = $this->cleanText((string) ($input['name'] ?? ''), 120);
        if ($name === '') {
            throw new InvalidArgumentException('Tag name is required.');
        }

        $slug = $this->uniqueTaxonomySlug(
            (string) ($input['slug'] ?? $name),
            fn (string $value, ?int $exclude): bool => $this->taxonomy->tagSlugExists($value, $exclude),
            $id
        );

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $this->cleanText((string) ($input['description'] ?? ''), 500),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function authorPayload(array $input, ?int $id = null): array
    {
        $name = $this->cleanText((string) ($input['display_name'] ?? ''), 160);
        if ($name === '') {
            throw new InvalidArgumentException('Author name is required.');
        }

        $email = $this->cleanText((string) ($input['email'] ?? ''), 190);
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Author email must be valid.');
        }

        $slug = $this->uniqueTaxonomySlug(
            (string) ($input['slug'] ?? $name),
            fn (string $value, ?int $exclude): bool => $this->taxonomy->authorSlugExists($value, $exclude),
            $id
        );

        return [
            'admin_user_id' => (int) ($input['admin_user_id'] ?? 0) ?: null,
            'display_name' => $name,
            'slug' => $slug,
            'title' => $this->cleanText((string) ($input['title'] ?? ''), 160),
            'bio' => $this->cleanText((string) ($input['bio'] ?? ''), 800),
            'avatar' => $this->cleanText((string) ($input['avatar'] ?? ''), 255),
            'email' => $email,
            'linkedin_url' => $this->validatedOptionalUrl((string) ($input['linkedin_url'] ?? ''), 'LinkedIn URL'),
            'x_url' => $this->validatedOptionalUrl((string) ($input['x_url'] ?? ''), 'X URL'),
            'is_active' => !empty($input['is_active']),
        ];
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function withPostRelations(array $post): array
    {
        $post['categories'] = $this->posts->categoriesForPost((int) $post['id']);
        $post['tags'] = $this->posts->tagsForPost((int) $post['id']);
        $post['category_ids'] = array_map('intval', array_column($post['categories'], 'id'));
        $post['tag_ids'] = array_map('intval', array_column($post['tags'], 'id'));
        $primary = array_values(array_filter($post['categories'], static fn (array $category): bool => !empty($category['is_primary'])));
        $post['primary_category_id'] = (int) ($primary[0]['id'] ?? ($post['category_ids'][0] ?? 0));

        return $post;
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function decoratePostSummary(array $post): array
    {
        $post['url'] = '/blog/' . (string) $post['slug'];
        $post['absolute_url'] = $this->permalink((string) $post['slug']);
        $post['reading_time'] = $this->readingTime((string) ($post['content'] ?? ''));
        $post['display_date'] = $post['publish_date'] ?? $post['published_at'] ?? $post['scheduled_at'] ?? $post['created_at'] ?? null;
        $post['image'] = $post['featured_image'] ?: (string) Config::get('seo.default_image', '');
        $post['image_alt'] = $post['featured_image_alt'] ?: $post['title'];

        return $post;
    }

    /**
     * @return array{content: string, toc: array<int, array{level: int, id: string, text: string}>}
     */
    private function contentWithToc(string $content): array
    {
        $toc = [];
        $used = [];

        $content = preg_replace_callback(
            '/<(h[23])([^>]*)>(.*?)<\/\1>/is',
            function (array $match) use (&$toc, &$used): string {
                $tag = strtolower($match[1]);
                $attributes = (string) $match[2];
                $inner = (string) $match[3];
                $text = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES, 'UTF-8'));

                if ($text === '') {
                    return $match[0];
                }

                if (preg_match('/\sid=["\']([^"\']+)["\']/i', $attributes, $idMatch) === 1) {
                    $id = $this->cleanSlug($idMatch[1]);
                } else {
                    $id = $this->slug($text);
                    $attributes .= ' id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '"';
                }

                $base = $id;
                $suffix = 2;
                while (isset($used[$id])) {
                    $id = $base . '-' . $suffix++;
                }
                $used[$id] = true;

                if (preg_match('/\sid=["\']([^"\']+)["\']/i', $attributes) === 1) {
                    $attributes = preg_replace('/\sid=["\'][^"\']+["\']/i', ' id="' . $id . '"', $attributes);
                }

                $toc[] = [
                    'level' => $tag === 'h2' ? 2 : 3,
                    'id' => $id,
                    'text' => $text,
                ];

                return '<' . $tag . $attributes . '>' . $inner . '</' . $tag . '>';
            },
            $content
        ) ?? $content;

        if (count($toc) < 3) {
            $toc = [];
        }

        return ['content' => $content, 'toc' => $toc];
    }

    /**
     * @param array<string, array<string, mixed>> $placements
     * @return array<string, array<string, mixed>>
     */
    private function filterAdsForWordCount(array $placements, int $wordCount): array
    {
        return array_filter(
            $placements,
            static fn (array $placement): bool => $wordCount >= (int) ($placement['min_word_count'] ?? 0)
        );
    }

    private function trackView(int $postId): void
    {
        try {
            $key = (string) Config::get('app.key', 'blog-view-key');
            $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
            $agent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
            $referrer = $this->cleanText((string) ($_SERVER['HTTP_REFERER'] ?? ''), 255);

            $this->posts->incrementViewCount($postId);
            $this->posts->recordView(
                $postId,
                $ip !== '' ? hash_hmac('sha256', $ip, $key) : null,
                $agent !== '' ? hash_hmac('sha256', $agent, $key) : null,
                $referrer !== '' ? $referrer : null
            );
        } catch (\Throwable) {
            // View tracking should never break article rendering.
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function listingSeo(string $title, string $description, string $query): array
    {
        $canonicalPath = $query === '' ? '/blog' : '/blog/search';

        return [
            'title' => $title . ' | Desnky Blog',
            'description' => $description,
            'canonical' => $this->baseUrl() . $canonicalPath,
            'type' => 'website',
            'robots' => $query === '' ? 'index, follow' : 'noindex, follow',
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::websiteSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $this->baseUrl() . '/',
                    'Blog' => $this->baseUrl() . '/blog',
                ]),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $post
     * @return array<string, mixed>
     */
    private function postSeo(array $post): array
    {
        $url = $post['canonical_url'] ?: $this->permalink((string) $post['slug']);
        $description = (string) ($post['meta_description'] ?: $post['excerpt'] ?: Config::get('seo.default_description', ''));
        $image = (string) ($post['og_image'] ?: $post['featured_image'] ?: Config::get('seo.default_image', ''));
        $title = (string) ($post['seo_title'] ?: $post['title']);

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $url,
            'image' => $image,
            'type' => 'article',
            'robots' => !empty($post['robots_index']) ? 'index, follow' : 'noindex, follow',
            'schema' => [
                SeoHelper::organizationSchema(),
                SeoHelper::breadcrumbSchema([
                    'Home' => $this->baseUrl() . '/',
                    'Blog' => $this->baseUrl() . '/blog',
                    (string) $post['title'] => $url,
                ]),
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => $post['title'],
                    'description' => $description,
                    'image' => $image,
                    'datePublished' => date(DATE_ATOM, strtotime((string) $post['display_date'])),
                    'dateModified' => date(DATE_ATOM, strtotime((string) $post['updated_at'])),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $post['author_name'] ?: Config::get('seo.site_name', 'Desnky Global Resources Ltd'),
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => Config::get('seo.site_name', 'Desnky Global Resources Ltd'),
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => Config::get('seo.logo', ''),
                        ],
                    ],
                    'mainEntityOfPage' => $url,
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cachedCategories(): array
    {
        return $this->cache?->remember('blog.categories.public', 300, fn () => $this->taxonomy->categories(false), ['blog'])
            ?? $this->taxonomy->categories(false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cachedTags(): array
    {
        return $this->cache?->remember('blog.tags.public', 300, fn () => $this->taxonomy->tags(), ['blog'])
            ?? $this->taxonomy->tags();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cachedFeatured(): array
    {
        return $this->cache?->remember('blog.posts.featured', 300, fn () => $this->posts->featured(), ['blog'])
            ?? $this->posts->featured();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cachedRecent(): array
    {
        return $this->cache?->remember('blog.posts.recent', 300, fn () => $this->posts->recent(), ['blog'])
            ?? $this->posts->recent();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cachedPopular(): array
    {
        return $this->cache?->remember('blog.posts.popular', 300, fn () => $this->posts->popular(), ['blog'])
            ?? $this->posts->popular();
    }

    private function flushBlogCache(): void
    {
        $this->cache?->flushTag('blog');
    }

    private function uniquePostSlug(string $value, ?int $excludeId = null): string
    {
        $slug = $this->slug($value);
        $base = $slug;
        $suffix = 2;

        while ($this->posts->slugExists($slug, $excludeId)) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    /**
     * @param callable(string, ?int): bool $exists
     */
    private function uniqueTaxonomySlug(string $value, callable $exists, ?int $excludeId = null): string
    {
        $slug = $this->slug($value);
        $base = $slug;
        $suffix = 2;

        while ($exists($slug, $excludeId)) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $value), '-'));

        return $slug !== '' ? $slug : 'post-' . date('YmdHis');
    }

    private function cleanSlug(string $value): string
    {
        return strtolower(trim((string) preg_replace('/[^a-z0-9-]+/i', '', $value), '-'));
    }

    private function cleanText(string $value, int $maxLength): string
    {
        $value = trim(strip_tags($value));
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $maxLength);
        }

        return substr($value, 0, $maxLength);
    }

    private function excerpt(string $input, string $content): string
    {
        $excerpt = $this->cleanText($input, 280);
        if ($excerpt !== '') {
            return $excerpt;
        }

        return $this->cleanText(html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8'), 220);
    }

    private function dateTimeOrNull(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);
        if ($timestamp === false) {
            throw new InvalidArgumentException('Please provide a valid date and time.');
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * @param mixed $value
     * @return array<int, int>
     */
    private function intList($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $value))));
    }

    private function validatedOptionalUrl(string $value, string $label): string
    {
        $url = $this->cleanText($value, 255);
        if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException($label . ' must be a valid URL.');
        }

        return $url;
    }

    private function wordCount(string $content): int
    {
        return str_word_count(html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8'));
    }

    private function baseUrl(): string
    {
        return rtrim((string) Config::get('seo.base_url', 'https://www.desnkygroup.com'), '/');
    }
}
