<?php
$posts = $posts ?? [];
$featuredPosts = $featuredPosts ?? [];
$categories = $categories ?? [];
$tags = $tags ?? [];
$recentPosts = $recentPosts ?? [];
$popularPosts = $popularPosts ?? [];
$filters = $filters ?? [];
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total' => 0];
$adPlacements = $adPlacements ?? [];

$pageUrl = static function (int $page) use ($filters): string {
    $path = '/blog';
    if (!empty($filters['category_slug'])) {
        $path = '/blog/category/' . rawurlencode((string) $filters['category_slug']);
    } elseif (!empty($filters['tag_slug'])) {
        $path = '/blog/tag/' . rawurlencode((string) $filters['tag_slug']);
    } elseif (!empty($filters['q'])) {
        $path = '/blog/search';
    }

    $query = [];
    if (!empty($filters['q'])) {
        $query['q'] = (string) $filters['q'];
    }
    if ($page > 1) {
        $query['page'] = $page;
    }

    return $path . ($query !== [] ? '?' . http_build_query($query) : '');
};

$card = function (array $post, bool $large = false): string {
    ob_start();
    ?>
    <article class="<?php echo $large ? 'blog-card blog-card--featured' : 'blog-card'; ?>">
        <a href="<?php echo $this->escape((string) $post['url']); ?>" class="blog-card__media">
            <?php if (!empty($post['image'])) : ?>
                <img src="<?php echo $this->escape((string) $post['image']); ?>" alt="<?php echo $this->escape((string) $post['image_alt']); ?>" loading="lazy" class="blog-card__image">
            <?php else : ?>
                <div class="blog-card__image blog-card__image--empty">Insight</div>
            <?php endif; ?>
        </a>
        <div class="blog-card__body">
            <?php if (!empty($post['category_name'])) : ?>
                <a href="/blog/category/<?php echo $this->escape((string) $post['category_slug']); ?>" class="blog-card__category"><?php echo $this->escape((string) $post['category_name']); ?></a>
            <?php endif; ?>
            <h2 class="<?php echo $large ? 'blog-card__title blog-card__title--large' : 'blog-card__title'; ?>">
                <a href="<?php echo $this->escape((string) $post['url']); ?>"><?php echo $this->escape((string) $post['title']); ?></a>
            </h2>
            <p class="blog-card__excerpt"><?php echo $this->escape((string) ($post['excerpt'] ?? '')); ?></p>
            <div class="blog-card__meta">
                <div class="flex items-center gap-3">
                    <?php if (!empty($post['author_avatar'])) : ?>
                        <img src="<?php echo $this->escape((string) $post['author_avatar']); ?>" alt="<?php echo $this->escape((string) ($post['author_name'] ?? 'Author')); ?>" class="h-8 w-8 rounded-full object-cover">
                    <?php else : ?>
                        <span class="inline-block h-8 w-8 rounded-full bg-gray-100"></span>
                    <?php endif; ?>
                    <div class="text-sm">
                        <div class="font-medium"><?php echo $this->escape((string) ($post['author_name'] ?? 'Desnky Editorial')); ?></div>
                        <div class="text-xs text-desnky-muted">
                            <time datetime="<?php echo $this->escape(date('Y-m-d', strtotime((string) $post['display_date']))); ?>"><?php echo $this->escape(date('M j, Y', strtotime((string) $post['display_date']))); ?></time>
                            <span aria-hidden="true"> · </span>
                            <span><?php echo (int) $post['reading_time']; ?> min read</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
    <?php
    return (string) ob_get_clean();
};
?>

<section class="section-band bg-white">
    <div class="container-page">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div>
                <div class="max-w-3xl">
                    <p class="eyebrow">Desnky Blog</p>
                    <h1 class="mt-3 section-heading"><?php echo $this->escape((string) ($heading ?? 'Insights')); ?></h1>
                    <p class="section-lead"><?php echo $this->escape((string) ($description ?? '')); ?></p>
                </div>

                <form action="/blog/search" method="get" class="blog-search mt-8" role="search">
                    <label for="blog-search" class="sr-only">Search blog posts</label>
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-desnky-muted">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'search', 'class' => 'h-5 w-5']); ?>
                    </span>
                    <input id="blog-search" name="q" type="search" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" placeholder="Search insights" class="form-field pl-10">
                    <button class="btn-primary">Search</button>
                </form>

                <?php if ($featuredPosts !== [] && empty($filters['q']) && empty($filters['category_slug']) && empty($filters['tag_slug'])) : ?>
                    <div class="mt-12">
                        <div class="mb-5 flex items-end justify-between gap-4">
                            <div>
                                <p class="eyebrow">Featured</p>
                                <h2 class="mt-2 text-2xl font-bold text-desnky-dark">Highlighted articles</h2>
                            </div>
                        </div>
                        <div class="grid gap-5 lg:grid-cols-2">
                            <?php foreach (array_slice($featuredPosts, 0, 2) as $post) : ?>
                                <?php echo $card($post, true); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php echo $this->partial('frontend/partials/ad-slot', [
                    'key' => 'blog_listing_between_sections',
                    'adPlacements' => $adPlacements,
                ]); ?>

                <div class="mt-12">
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="eyebrow">Latest</p>
                            <h2 class="mt-2 text-2xl font-bold text-desnky-dark">Articles and updates</h2>
                        </div>
                        <p class="text-sm text-desnky-muted"><?php echo (int) ($pagination['total'] ?? 0); ?> result<?php echo (int) ($pagination['total'] ?? 0) === 1 ? '' : 's'; ?></p>
                    </div>

                    <?php if ($posts !== []) : ?>
                        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            <?php foreach ($posts as $post) : ?>
                                <?php echo $card($post); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-state">
                            <p class="text-lg font-bold text-desnky-dark">No articles found.</p>
                            <p class="mt-2 text-sm text-desnky-muted">Try another search term or browse all blog posts.</p>
                            <a href="/blog" class="btn-primary mt-5">Browse all posts</a>
                        </div>
                    <?php endif; ?>

                    <?php if ((int) ($pagination['total_pages'] ?? 1) > 1) : ?>
                        <nav class="mt-10 flex flex-wrap items-center justify-center gap-2" aria-label="Blog pagination">
                            <?php for ($i = 1; $i <= (int) $pagination['total_pages']; $i++) : ?>
                                <a
                                    href="<?php echo $this->escape($pageUrl($i)); ?>"
                                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border px-3 text-sm font-semibold <?php echo (int) $pagination['page'] === $i ? 'border-desnky-primary bg-primary text-white' : 'border-gray-200 bg-white text-desnky-ink hover:border-desnky-primary'; ?>"
                                    <?php echo (int) $pagination['page'] === $i ? 'aria-current="page"' : ''; ?>
                                >
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="space-y-8">
                <div class="blog-sidebar-panel">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark">Categories</h2>
                    <nav class="mt-4 grid gap-2" aria-label="Blog categories">
                        <a href="/blog" class="blog-filter-link <?php echo empty($filters['category_slug']) && empty($filters['tag_slug']) ? 'blog-filter-link--active' : ''; ?>">All articles</a>
                        <?php foreach ($categories as $category) : ?>
                            <?php if ((int) ($category['post_count'] ?? 0) === 0) { continue; } ?>
                            <a href="/blog/category/<?php echo $this->escape((string) $category['slug']); ?>" class="blog-filter-link <?php echo ($filters['category_slug'] ?? '') === ($category['slug'] ?? '') ? 'blog-filter-link--active' : ''; ?>">
                                <span><?php echo $this->escape((string) $category['name']); ?></span>
                                <span><?php echo (int) $category['post_count']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <?php if ($popularPosts !== []) : ?>
                    <div class="blog-sidebar-panel">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark">Popular posts</h2>
                        <div class="mt-4 space-y-4">
                            <?php foreach ($popularPosts as $post) : ?>
                                <a href="<?php echo $this->escape((string) $post['url']); ?>" class="block rounded-md p-2 hover:bg-desnky-surface">
                                    <span class="block text-sm font-bold leading-5 text-desnky-dark"><?php echo $this->escape((string) $post['title']); ?></span>
                                    <span class="mt-1 block text-xs text-desnky-muted"><?php echo (int) $post['reading_time']; ?> min read</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($tags !== []) : ?>
                    <div class="blog-sidebar-panel">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark">Tags</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <?php foreach ($tags as $tag) : ?>
                                <?php if ((int) ($tag['post_count'] ?? 0) === 0) { continue; } ?>
                                <a href="/blog/tag/<?php echo $this->escape((string) $tag['slug']); ?>" class="chip <?php echo ($filters['tag_slug'] ?? '') === ($tag['slug'] ?? '') ? 'chip-active' : ''; ?>">#<?php echo $this->escape((string) $tag['name']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="rounded-lg border border-gray-200 bg-desnky-surface p-5">
                    <h2 class="text-lg font-bold text-desnky-dark">Get practical updates</h2>
                    <p class="mt-2 text-sm leading-6 text-desnky-muted">Receive procurement, engineering, HSE, ICT and agro operations insights from Desnky.</p>
                    <form action="/newsletter" method="post" class="mt-4 space-y-3" data-newsletter-form>
                        <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($csrf_token ?? '')); ?>">
                        <label class="sr-only" for="blog-newsletter">Email address</label>
                        <input id="blog-newsletter" name="email" type="email" required class="form-field" placeholder="you@company.com">
                        <button class="btn-primary w-full">Subscribe</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>
