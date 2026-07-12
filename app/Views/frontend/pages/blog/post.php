<?php
$post = $post ?? [];
$relatedPosts = $relatedPosts ?? [];
$adjacent = $adjacent ?? ['previous' => null, 'next' => null];
$recentPosts = $recentPosts ?? [];
$popularPosts = $popularPosts ?? [];
$adPlacements = $adPlacements ?? [];
$isPreview = !empty($isPreview);

$shareUrl = urlencode((string) ($post['absolute_url'] ?? ''));
$shareTitle = urlencode((string) ($post['title'] ?? ''));
$adSlot = function (string $key) use ($adPlacements, $isPreview): string {
    if ($isPreview) {
        return '';
    }

    return $this->partial('frontend/partials/ad-slot', [
        'key' => $key,
        'adPlacements' => $adPlacements,
    ]);
};

$articleContent = (string) ($post['content'] ?? '');
$inContentAd = $adSlot('article_in_content');
if ($inContentAd !== '' && preg_match('/<\/h2>/i', $articleContent) === 1) {
    $articleContent = preg_replace('/<\/h2>/i', '</h2>' . $inContentAd, $articleContent, 1) ?? $articleContent;
}
?>

<?php if ($isPreview) : ?>
    <div class="bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-900">
        Preview mode. This page is not indexed and advertisements are disabled.
    </div>
<?php endif; ?>

<article class="bg-white">
    <header class="section-band pb-10">
        <div class="container-page">
            <nav aria-label="Breadcrumb" class="mb-8 text-sm text-desnky-muted">
                <a href="/" class="hover:text-desnky-primary">Home</a>
                <span class="mx-2" aria-hidden="true">/</span>
                <a href="/blog" class="hover:text-desnky-primary">Blog</a>
                <?php if (!empty($post['category_name'])) : ?>
                    <span class="mx-2" aria-hidden="true">/</span>
                    <a href="/blog/category/<?php echo $this->escape((string) $post['category_slug']); ?>" class="hover:text-desnky-primary"><?php echo $this->escape((string) $post['category_name']); ?></a>
                <?php endif; ?>
            </nav>

            <div class="max-w-4xl">
                <?php if (!empty($post['category_name'])) : ?>
                    <a href="/blog/category/<?php echo $this->escape((string) $post['category_slug']); ?>" class="eyebrow"><?php echo $this->escape((string) $post['category_name']); ?></a>
                <?php endif; ?>
                <h1 class="mt-4 text-4xl font-extrabold leading-tight text-desnky-dark sm:text-5xl"><?php echo $this->escape((string) ($post['title'] ?? '')); ?></h1>
                <?php if (!empty($post['excerpt'])) : ?>
                    <p class="mt-6 max-w-3xl text-lg leading-8 text-desnky-muted"><?php echo $this->escape((string) $post['excerpt']); ?></p>
                <?php endif; ?>

                <div class="mt-7 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-desnky-muted">
                    <span class="font-semibold text-desnky-dark"><?php echo $this->escape((string) ($post['author_name'] ?? 'Desnky Editorial')); ?></span>
                    <span aria-hidden="true">/</span>
                    <time datetime="<?php echo $this->escape(date(DATE_ATOM, strtotime((string) $post['display_date']))); ?>">
                        <?php echo $this->escape(date('F j, Y', strtotime((string) $post['display_date']))); ?>
                    </time>
                    <span aria-hidden="true">/</span>
                    <span>Updated <?php echo $this->escape(date('F j, Y', strtotime((string) $post['updated_at']))); ?></span>
                    <span aria-hidden="true">/</span>
                    <span><?php echo (int) ($post['reading_time'] ?? 1); ?> min read</span>
                </div>
            </div>
        </div>
    </header>

    <?php if (!empty($post['featured_image'])) : ?>
        <div class="container-page">
            <figure class="overflow-hidden rounded-lg bg-desnky-surface">
                <img
                    src="<?php echo $this->escape((string) $post['featured_image']); ?>"
                    alt="<?php echo $this->escape((string) ($post['image_alt'] ?? $post['title'])); ?>"
                    class="aspect-[16/8] w-full object-cover"
                    loading="eager"
                    decoding="async"
                >
            </figure>
        </div>
    <?php endif; ?>

    <div class="container-page py-12">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,48rem)_20rem] xl:grid-cols-[minmax(0,52rem)_22rem]">
            <div class="min-w-0">
                <?php echo $adSlot('article_after_intro'); ?>

                <?php if (!empty($post['toc'])) : ?>
                    <nav class="mb-10 rounded-lg border border-gray-200 bg-desnky-surface p-5" aria-label="Table of contents">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark">On this page</h2>
                        <ol class="mt-4 grid gap-2 text-sm">
                            <?php foreach ($post['toc'] as $item) : ?>
                                <li class="<?php echo (int) $item['level'] === 3 ? 'ml-4' : ''; ?>">
                                    <a href="#<?php echo $this->escape((string) $item['id']); ?>" class="text-desnky-muted hover:text-desnky-primary">
                                        <?php echo $this->escape((string) $item['text']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>

                <div class="blog-prose">
                    <?php echo $articleContent; ?>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-3 border-y border-gray-200 py-5">
                    <span class="text-sm font-bold text-desnky-dark">Share</span>
                    <a class="blog-share-link" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">LinkedIn</a>
                    <a class="blog-share-link" href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" rel="noopener">X</a>
                    <a class="blog-share-link" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">Facebook</a>
                    <a class="blog-share-link" href="mailto:?subject=<?php echo $shareTitle; ?>&body=<?php echo $shareUrl; ?>">Email</a>
                </div>

                <?php if (!empty($post['tags'])) : ?>
                    <div class="mt-8 flex flex-wrap gap-2">
                        <?php foreach ($post['tags'] as $tag) : ?>
                            <a href="/blog/tag/<?php echo $this->escape((string) $tag['slug']); ?>" class="chip">#<?php echo $this->escape((string) $tag['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($post['author_name'])) : ?>
                    <section class="mt-10 rounded-lg border border-gray-200 bg-desnky-surface p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <?php if (!empty($post['author_avatar'])) : ?>
                                <img src="<?php echo $this->escape((string) $post['author_avatar']); ?>" alt="" class="h-16 w-16 rounded-full object-cover">
                            <?php else : ?>
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-desnky-primary text-xl font-bold text-white">
                                    <?php echo $this->escape(strtoupper(substr((string) $post['author_name'], 0, 1))); ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h2 class="text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) $post['author_name']); ?></h2>
                                <?php if (!empty($post['author_title'])) : ?>
                                    <p class="text-sm font-semibold text-desnky-primary"><?php echo $this->escape((string) $post['author_title']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($post['author_bio'])) : ?>
                                    <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $post['author_bio']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <nav class="mt-10 grid gap-4 sm:grid-cols-2" aria-label="Previous and next articles">
                    <?php if (!empty($adjacent['previous'])) : ?>
                        <a href="/blog/<?php echo $this->escape((string) $adjacent['previous']['slug']); ?>" class="rounded-lg border border-gray-200 p-5 hover:border-desnky-primary">
                            <span class="text-xs font-bold uppercase tracking-wide text-desnky-muted">Previous</span>
                            <span class="mt-2 block font-bold text-desnky-dark"><?php echo $this->escape((string) $adjacent['previous']['title']); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($adjacent['next'])) : ?>
                        <a href="/blog/<?php echo $this->escape((string) $adjacent['next']['slug']); ?>" class="rounded-lg border border-gray-200 p-5 text-right hover:border-desnky-primary">
                            <span class="text-xs font-bold uppercase tracking-wide text-desnky-muted">Next</span>
                            <span class="mt-2 block font-bold text-desnky-dark"><?php echo $this->escape((string) $adjacent['next']['title']); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>

                <?php echo $adSlot('article_before_related'); ?>

                <?php if ($relatedPosts !== []) : ?>
                    <section class="mt-12">
                        <div class="mb-5">
                            <p class="eyebrow">Related</p>
                            <h2 class="mt-2 text-2xl font-bold text-desnky-dark">Continue reading</h2>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-3">
                            <?php foreach ($relatedPosts as $related) : ?>
                                <a href="<?php echo $this->escape((string) $related['url']); ?>" class="group rounded-lg border border-gray-200 bg-white p-4 hover:border-desnky-primary">
                                    <span class="text-xs font-bold uppercase tracking-wide text-desnky-primary"><?php echo $this->escape((string) ($related['category_name'] ?? 'Article')); ?></span>
                                    <span class="mt-2 block font-bold leading-6 text-desnky-dark group-hover:text-desnky-primary"><?php echo $this->escape((string) $related['title']); ?></span>
                                    <span class="mt-2 block text-xs text-desnky-muted"><?php echo (int) $related['reading_time']; ?> min read</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>

            <aside class="space-y-8">
                <?php echo $adSlot('article_sidebar'); ?>

                <?php if ($recentPosts !== []) : ?>
                    <div class="blog-sidebar-panel">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark">Recent posts</h2>
                        <div class="mt-4 space-y-4">
                            <?php foreach ($recentPosts as $recent) : ?>
                                <a href="<?php echo $this->escape((string) $recent['url']); ?>" class="block rounded-md p-2 hover:bg-desnky-surface">
                                    <span class="block text-sm font-bold leading-5 text-desnky-dark"><?php echo $this->escape((string) $recent['title']); ?></span>
                                    <span class="mt-1 block text-xs text-desnky-muted"><?php echo $this->escape(date('M j, Y', strtotime((string) $recent['display_date']))); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($popularPosts !== []) : ?>
                    <div class="blog-sidebar-panel">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark">Popular posts</h2>
                        <div class="mt-4 space-y-4">
                            <?php foreach ($popularPosts as $popular) : ?>
                                <a href="<?php echo $this->escape((string) $popular['url']); ?>" class="block rounded-md p-2 hover:bg-desnky-surface">
                                    <span class="block text-sm font-bold leading-5 text-desnky-dark"><?php echo $this->escape((string) $popular['title']); ?></span>
                                    <span class="mt-1 block text-xs text-desnky-muted"><?php echo (int) $popular['reading_time']; ?> min read</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="rounded-lg border border-gray-200 bg-desnky-surface p-5">
                    <h2 class="text-lg font-bold text-desnky-dark">Need operational support?</h2>
                    <p class="mt-2 text-sm leading-6 text-desnky-muted">Discuss procurement, engineering, HSE, ICT or agro requirements with our team.</p>
                    <a href="/contact" class="btn-primary mt-4 w-full">Contact us</a>
                </div>
            </aside>
        </div>
    </div>
</article>
