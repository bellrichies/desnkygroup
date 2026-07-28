<?php
/** @var array<string, mixed> $post */
$post = isset($post) && is_array($post) ? $post : [];
$relatedPosts = isset($relatedPosts) && is_array($relatedPosts) ? $relatedPosts : [];
$adjacent = isset($adjacent) && is_array($adjacent) ? $adjacent : ['previous' => null, 'next' => null];
$recentPosts = isset($recentPosts) && is_array($recentPosts) ? $recentPosts : [];
$popularPosts = isset($popularPosts) && is_array($popularPosts) ? $popularPosts : [];
$adPlacements = isset($adPlacements) && is_array($adPlacements) ? $adPlacements : [];
$isPreview = !empty($isPreview);

$title = (string) ($post['title'] ?? '');
$absoluteUrl = (string) ($post['absolute_url'] ?? '');
$shareUrl = rawurlencode($absoluteUrl);
$shareTitle = rawurlencode($title);
$displayDate = (string) ($post['display_date'] ?? $post['created_at'] ?? 'now');
$updatedDate = (string) ($post['updated_at'] ?? $displayDate);
$featuredImage = (string) ($post['featured_image'] ?? '');
$imageAlt = (string) ($post['image_alt'] ?? $title);
$authorName = (string) ($post['author_name'] ?? 'Desnky Editorial');
$authorInitial = strtoupper(substr($authorName, 0, 1));

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
    <div class="relative z-50 border-b border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm font-bold text-amber-900">
        Preview mode — this article is not indexed and advertisements are disabled.
    </div>
<?php endif; ?>

<div id="article-progress" class="fixed inset-x-0 top-0 z-[70] h-1 origin-left scale-x-0 bg-gradient-to-r from-desnky-primary via-fuchsia-500 to-desnky-secondary" aria-hidden="true"></div>

<article id="article-root" class="overflow-hidden bg-[#f5f3f7]">
    <header class="relative overflow-hidden bg-desnky-dark pb-28 pt-10 text-white sm:pb-36 sm:pt-14 lg:pb-44">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -right-32 -top-40 h-[34rem] w-[34rem] rounded-full bg-desnky-primary/30 blur-3xl"></div>
            <div class="absolute -bottom-48 -left-40 h-[30rem] w-[30rem] rounded-full bg-desnky-secondary/15 blur-3xl"></div>
            <div class="article-hero-grid absolute inset-0 opacity-20"></div>
        </div>

        <div class="container-page relative">
            <nav aria-label="Breadcrumb" class="mb-10 flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[.14em] text-white/55">
                <a href="/" class="transition hover:text-white">Home</a>
                <span aria-hidden="true">—</span>
                <a href="/blog" class="transition hover:text-white">Journal</a>
                <?php if (!empty($post['category_name'])) : ?>
                    <span aria-hidden="true">—</span>
                    <a href="/blog/category/<?php echo $this->escape((string) $post['category_slug']); ?>" class="transition hover:text-white"><?php echo $this->escape((string) $post['category_name']); ?></a>
                <?php endif; ?>
            </nav>

            <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_17rem] lg:items-end">
                <div class="max-w-5xl">
                    <?php if (!empty($post['category_name'])) : ?>
                        <a href="/blog/category/<?php echo $this->escape((string) $post['category_slug']); ?>" class="inline-flex rounded-full border border-white/15 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[.16em] text-desnky-primary-200 backdrop-blur transition hover:bg-white/15">
                            <?php echo $this->escape((string) $post['category_name']); ?>
                        </a>
                    <?php endif; ?>
                    <h1 class="mt-6 max-w-5xl text-4xl font-black leading-[1.06] tracking-[-.035em] text-white sm:text-5xl lg:text-6xl xl:text-7xl">
                        <?php echo $this->escape($title); ?>
                    </h1>
                    <?php if (!empty($post['excerpt'])) : ?>
                        <p class="mt-7 max-w-3xl text-lg leading-8 text-white/70 sm:text-xl sm:leading-9">
                            <?php echo $this->escape((string) $post['excerpt']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="border-l border-white/15 pl-5">
                    <p class="text-xs font-bold uppercase tracking-[.16em] text-white/40">Published</p>
                    <time datetime="<?php echo $this->escape(date(DATE_ATOM, strtotime($displayDate))); ?>" class="mt-2 block text-base font-bold text-white">
                        <?php echo $this->escape(date('F j, Y', strtotime($displayDate))); ?>
                    </time>
                    <div class="mt-5 flex items-center gap-3">
                        <?php if (!empty($post['author_avatar'])) : ?>
                            <img src="<?php echo $this->escape((string) $post['author_avatar']); ?>" alt="" class="h-11 w-11 rounded-full border-2 border-white/20 object-cover">
                        <?php else : ?>
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-sm font-black text-white"><?php echo $this->escape($authorInitial); ?></span>
                        <?php endif; ?>
                        <div>
                            <p class="text-sm font-bold text-white"><?php echo $this->escape($authorName); ?></p>
                            <p class="mt-0.5 text-xs text-white/50"><?php echo (int) ($post['reading_time'] ?? 1); ?> min read</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php if ($featuredImage !== '') : ?>
        <div class="container-page relative z-10 -mt-20 sm:-mt-28 lg:-mt-36">
            <figure class="article-featured-media relative overflow-hidden rounded-[1.5rem] bg-desnky-surface shadow-[0_30px_80px_rgba(29,18,40,.22)] sm:rounded-[2rem]">
                <img src="<?php echo $this->escape($featuredImage); ?>" alt="<?php echo $this->escape($imageAlt); ?>" class="aspect-[16/9] w-full object-cover lg:aspect-[2/1]" width="1440" height="720" loading="eager" fetchpriority="high" decoding="async">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-desnky-dark/25 via-transparent to-transparent"></div>
                <figcaption class="absolute bottom-0 right-0 rounded-tl-2xl bg-white/90 px-4 py-2 text-xs font-semibold text-desnky-muted backdrop-blur">
                    Desnky Journal
                </figcaption>
            </figure>
        </div>
    <?php endif; ?>

    <div class="container-page relative z-10 py-12 sm:py-16">
        <div class="grid gap-8 lg:grid-cols-[3.25rem_minmax(0,49rem)_minmax(15rem,20rem)] lg:items-start xl:gap-12">
            <aside class="hidden lg:block">
                <div class="sticky top-28 grid gap-2" aria-label="Share article">
                    <span class="mb-1 text-center text-[10px] font-black uppercase tracking-[.16em] text-desnky-muted">Share</span>
                    <a class="article-share-icon" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $shareUrl; ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn">in</a>
                    <a class="article-share-icon" href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" rel="noopener" aria-label="Share on X">X</a>
                    <a class="article-share-icon" href="mailto:?subject=<?php echo $shareTitle; ?>&body=<?php echo $shareUrl; ?>" aria-label="Share by email">@</a>
                    <button type="button" class="article-share-icon" data-copy-article aria-label="Copy article link" title="Copy link">↗</button>
                </div>
            </aside>

            <main class="min-w-0">
                <div class="rounded-[1.5rem] border border-white bg-white px-5 py-7 shadow-[0_16px_50px_rgba(29,18,40,.07)] sm:px-9 sm:py-10 lg:px-12 lg:py-12">
                    <div class="mb-9 flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-desnky-primary-50 text-sm font-black text-desnky-primary"><?php echo $this->escape($authorInitial); ?></span>
                            <div>
                                <p class="text-sm font-bold text-desnky-dark"><?php echo $this->escape($authorName); ?></p>
                                <p class="text-xs text-desnky-muted">Updated <?php echo $this->escape(date('M j, Y', strtotime($updatedDate))); ?></p>
                            </div>
                        </div>
                        <span class="rounded-full bg-desnky-surface px-3 py-1.5 text-xs font-bold text-desnky-muted"><?php echo (int) ($post['reading_time'] ?? 1); ?> minute read</span>
                    </div>

                    <?php echo $adSlot('article_after_intro'); ?>

                    <div class="blog-prose article-prose">
                        <?php echo $articleContent; ?>
                    </div>

                    <?php if (!empty($post['tags'])) : ?>
                        <div class="mt-12 border-t border-gray-100 pt-7">
                            <p class="mb-3 text-xs font-black uppercase tracking-[.16em] text-desnky-muted">Filed under</p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($post['tags'] as $tag) : ?>
                                    <a href="/blog/tag/<?php echo $this->escape((string) $tag['slug']); ?>" class="chip">#<?php echo $this->escape((string) $tag['name']); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-7 flex flex-wrap items-center gap-2 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm lg:hidden">
                    <span class="mr-1 text-xs font-black uppercase tracking-wide text-desnky-muted">Share</span>
                    <a class="blog-share-link" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">LinkedIn</a>
                    <a class="blog-share-link" href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareTitle; ?>" target="_blank" rel="noopener">X</a>
                    <button type="button" class="blog-share-link" data-copy-article>Copy link</button>
                </div>



                <nav class="mt-8 grid gap-4 sm:grid-cols-2" aria-label="Previous and next articles">
                    <?php if (!empty($adjacent['previous'])) : ?>
                        <a href="/blog/<?php echo $this->escape((string) $adjacent['previous']['slug']); ?>" class="article-adjacent-card">
                            <span class="text-xs font-black uppercase tracking-[.14em] text-desnky-muted">← Previous story</span>
                            <span class="mt-3 block font-black leading-6 text-desnky-dark"><?php echo $this->escape((string) $adjacent['previous']['title']); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($adjacent['next'])) : ?>
                        <a href="/blog/<?php echo $this->escape((string) $adjacent['next']['slug']); ?>" class="article-adjacent-card text-right">
                            <span class="text-xs font-black uppercase tracking-[.14em] text-desnky-muted">Next story →</span>
                            <span class="mt-3 block font-black leading-6 text-desnky-dark"><?php echo $this->escape((string) $adjacent['next']['title']); ?></span>
                        </a>
                    <?php endif; ?>
                </nav>
            </main>

            <aside class="space-y-6 lg:sticky lg:top-24">
                <?php if (!empty($post['toc'])) : ?>
                    <nav class="article-toc" aria-label="Table of contents">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xs font-black uppercase tracking-[.16em] text-desnky-dark">In this story</h2>
                            <span class="text-xs font-bold text-desnky-primary"><?php echo count($post['toc']); ?> sections</span>
                        </div>
                        <ol class="mt-5 grid gap-1">
                            <?php foreach ($post['toc'] as $index => $item) : ?>
                                <li class="<?php echo (int) $item['level'] === 3 ? 'ml-4' : ''; ?>">
                                    <a href="#<?php echo $this->escape((string) $item['id']); ?>" class="article-toc-link">
                                        <span class="text-[10px] font-black text-desnky-primary/50"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                                        <span><?php echo $this->escape((string) $item['text']); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>

                <?php echo $adSlot('article_sidebar'); ?>

                <?php if ($popularPosts !== []) : ?>
                    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <h2 class="text-xs font-black uppercase tracking-[.16em] text-desnky-dark">Most read</h2>
                        <div class="mt-4 divide-y divide-gray-100">
                            <?php foreach (array_slice($popularPosts, 0, 4) as $index => $popular) : ?>
                                <a href="<?php echo $this->escape((string) $popular['url']); ?>" class="group grid grid-cols-[1.75rem_1fr] gap-3 py-3">
                                    <span class="text-xl font-black text-desnky-primary/25">0<?php echo $index + 1; ?></span>
                                    <span>
                                        <span class="block text-sm font-bold leading-5 text-desnky-dark group-hover:text-desnky-primary"><?php echo $this->escape((string) $popular['title']); ?></span>
                                        <span class="mt-1 block text-xs text-desnky-muted"><?php echo (int) $popular['reading_time']; ?> min read</span>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-desnky-primary to-desnky-dark p-6 text-white shadow-card">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full border border-white/10"></div>
                    <p class="text-xs font-black uppercase tracking-[.16em] text-white/55">Work with Desnky</p>
                    <h2 class="mt-3 text-xl font-black text-white">Turn insight into action.</h2>
                    <p class="mt-3 text-sm leading-6 text-white/70">Discuss procurement, engineering, ICT, energy or agro requirements with our team.</p>
                    <a href="/contact" class="mt-5 inline-flex rounded-xl bg-white px-4 py-2.5 text-sm font-black text-desnky-dark transition hover:-translate-y-0.5 hover:shadow-lg">Start a conversation</a>
                </div>
            </aside>
        </div>

        <?php echo $adSlot('article_before_related'); ?>

        <?php if ($relatedPosts !== []) : ?>
            <section class="mt-16 border-t border-gray-200 pt-12">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div><p class="eyebrow">Keep exploring</p><h2 class="mt-2 text-3xl font-black tracking-tight text-desnky-dark sm:text-4xl">Stories selected for you</h2></div>
                    <a href="/blog" class="btn-ghost">View all insights →</a>
                </div>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    <?php foreach (array_slice($relatedPosts, 0, 3) as $related) : ?>
                        <a href="<?php echo $this->escape((string) $related['url']); ?>" class="group overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-card">
                            <div class="overflow-hidden bg-desnky-surface">
                                <?php if (!empty($related['image'])) : ?>
                                    <img src="<?php echo $this->escape((string) $related['image']); ?>" alt="<?php echo $this->escape((string) ($related['image_alt'] ?? $related['title'])); ?>" class="aspect-[16/10] w-full object-cover transition duration-slow group-hover:scale-105" loading="lazy" decoding="async">
                                <?php else : ?>
                                    <div class="flex aspect-[16/10] items-center justify-center text-xs font-black uppercase tracking-widest text-desnky-primary">Desnky Journal</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-5">
                                <span class="text-xs font-black uppercase tracking-[.12em] text-desnky-primary"><?php echo $this->escape((string) ($related['category_name'] ?? 'Insight')); ?></span>
                                <h3 class="mt-3 text-lg font-black leading-6 text-desnky-dark group-hover:text-desnky-primary"><?php echo $this->escape((string) $related['title']); ?></h3>
                                <p class="mt-4 text-xs font-semibold text-desnky-muted"><?php echo (int) $related['reading_time']; ?> min read · Read story →</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</article>

<div id="copy-feedback" class="pointer-events-none fixed bottom-24 left-1/2 z-toast -translate-x-1/2 translate-y-3 rounded-full bg-desnky-dark px-4 py-2 text-sm font-bold text-white opacity-0 shadow-lg transition" role="status" aria-live="polite">Link copied</div>

<script>
(function () {
    const progress = document.getElementById('article-progress');
    const root = document.getElementById('article-root');
    const feedback = document.getElementById('copy-feedback');
    let ticking = false;

    function updateProgress() {
        const start = root.offsetTop;
        const distance = Math.max(1, root.offsetHeight - window.innerHeight);
        const value = Math.min(1, Math.max(0, (window.scrollY - start) / distance));
        progress.style.transform = `scaleX(${value})`;
        ticking = false;
    }
    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateProgress);
            ticking = true;
        }
    }, { passive: true });
    updateProgress();

    document.querySelectorAll('[data-copy-article]').forEach(button => {
        button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(<?php echo $this->escapeJson($absoluteUrl); ?>);
                feedback.textContent = 'Link copied';
            } catch (error) {
                feedback.textContent = 'Copy the URL from your browser';
            }
            feedback.classList.remove('translate-y-3', 'opacity-0');
            window.setTimeout(() => feedback.classList.add('translate-y-3', 'opacity-0'), 1800);
        });
    });
})();
</script>
