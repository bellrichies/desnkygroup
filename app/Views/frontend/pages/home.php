<?php
$hero           ??= [];
$servicesIntro  ??= [];
$whyChooseUs    ??= [];
$hseCommitment  ??= [];
$projectsIntro  ??= [];
$clientsSection  ??= [];
$cta             ??= [];
$heroSlides      ??= [];
$services        ??= [];
$projects        ??= [];
$blogPosts       ??= [];
$trustedClients  ??= [];
$clients           = $trustedClients;
$testimonials    ??= [];

// CMS-overridable stats band; sensible defaults keep the band populated.
$stats     ??= [];
$statItems   = $stats['items'] ?? [
    ['value' => '15',  'suffix' => '+', 'label' => 'Years of operation'],
    ['value' => '200', 'suffix' => '+', 'label' => 'Projects delivered'],
    ['value' => '6',   'suffix' => '',  'label' => 'Industrial sectors'],
    ['value' => '100', 'suffix' => '%', 'label' => 'HSE commitment'],
];

// Map service slugs to curated sector icons for consistent iconography.
$sectorIcons = [
    'engineering'          => 'cog',
    'energy-solutions'     => 'bolt',
    'procurement'          => 'truck',
    'hse-safety'           => 'shield-check',
    'ict-solutions'        => 'server',
    'agro-food-processing' => 'leaf',
];
$iconFor = static fn (string $slug): string => $sectorIcons[$slug] ?? 'sparkles';
?>

<?php
// Homepage hero slides are managed from the dedicated database table.
$sliderSlides = [];

foreach ($heroSlides as $slide) {
    $sliderSlides[] = [
        'type'            => 'admin',
        'eyebrow'         => $hero['eyebrow'] ?? '',
        'heading'         => (string) ($slide['heading'] ?? ''),
        'text'            => (string) ($slide['caption'] ?? ''),
        'image'           => (string) ($slide['background_image'] ?? ''),
        'image_alt'       => (string) ($slide['heading'] ?? 'Homepage hero background'),
        'primary_label'   => (string) ($slide['primary_cta_label'] ?? ''),
        'primary_url'     => (string) ($slide['primary_cta_url'] ?? ''),
        'secondary_label' => (string) ($slide['secondary_cta_label'] ?? ''),
        'secondary_url'   => (string) ($slide['secondary_cta_url'] ?? ''),
    ];
}

// Encode for Alpine.js x-data — all values are escaped in PHP before encoding.
$slidesJson = json_encode(array_map(static function (array $s): array {
    return array_map(static fn ($v) => is_string($v) ? htmlspecialchars($v, ENT_QUOTES, 'UTF-8') : $v, $s);
}, $sliderSlides), JSON_HEX_TAG | JSON_HEX_AMP);
?>

<?php if ($sliderSlides !== []) : ?>
<section
    class="hero-slider"
    x-data='heroSlider(<?php echo $slidesJson; ?>)'
    x-init="startAutoplay()"
    @focusin="holdAutoplay()"
    @focusout="resumeAutoplay()"
    @touchstart.passive="touchStart($event)"
    @touchend.passive="touchEnd($event)"
    @keydown.arrow-left.window="prev()"
    @keydown.arrow-right.window="next()"
    aria-label="Homepage hero"
    aria-roledescription="carousel"
    x-bind:aria-live="_paused ? 'polite' : 'off'"
>
    <!-- Slide backgrounds (cross-fade) -->
    <template x-for="(slide, i) in slides" :key="i">
        <div
            x-show="current === i"
            x-transition:enter="transition-opacity duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-500"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="hero-slider__background"
            aria-hidden="true"
        >
            <img
                x-show="slide.image"
                x-bind:src="slide.image || ''"
                x-bind:alt="slide.image_alt || ''"
                alt="Featured service visual"
                class="hero-slider__image"
                x-bind:loading="i === 0 ? 'eager' : 'lazy'"
                x-bind:fetchpriority="i === 0 ? 'high' : 'auto'"
                decoding="async"
            >
        </div>
    </template>
    <div class="hero-slider__overlay" aria-hidden="true"></div>
    <div class="hero-slider__vignette" aria-hidden="true"></div>

    <!-- Slide content -->
    <div class="container-page hero-slider__inner">
        <template x-for="(slide, i) in slides" :key="'c' + i">
            <div
                x-show="current === i"
                x-transition:enter="transition duration-700 ease-out"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition duration-slow ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="hero-slider__copy"
                role="group"
                aria-roledescription="slide"
                x-bind:aria-label="'Slide ' + (i + 1) + ' of ' + slides.length"
            >
                <p class="eyebrow-on-dark" x-text="slide.eyebrow"></p>
                <h1 class="hero-slider__title" x-text="slide.heading"></h1>
                <p class="hero-slider__text" x-text="slide.text" x-show="slide.text"></p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a
                        x-bind:href="slide.primary_url"
                        class="btn-primary"
                        x-text="slide.primary_label"
                        x-show="slide.primary_label"
                        data-analytics-event="hero_cta"
                    ></a>
                    <a
                        x-bind:href="slide.secondary_url"
                        class="btn-secondary-on-dark"
                        x-text="slide.secondary_label"
                        x-show="slide.secondary_label && slide.secondary_url"
                    ></a>
                </div>
            </div>
        </template>
    </div>

    <div class="hero-slider__nav" x-show="slides.length > 1" x-cloak>
        <div class="container-page flex items-center justify-between gap-4">
            <!-- Dot indicators -->
            <div class="hero-slider__dots" role="tablist" aria-label="Slides">
                <template x-for="(_, i) in slides" :key="'dot' + i">
                    <button
                        type="button"
                        role="tab"
                        @click="goTo(i); pauseAutoplay()"
                        :aria-selected="current === i"
                        :tabindex="current === i ? 0 : -1"
                        :aria-label="'Go to slide ' + (i + 1)"
                        :class="current === i ? 'hero-slider__dot hero-slider__dot--active' : 'hero-slider__dot'"
                    ></button>
                </template>
            </div>

            <!-- Prev / Next controls -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="prev(); pauseAutoplay()"
                    class="hero-slider__control"
                    aria-label="Previous slide"
                >
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-right', 'class' => 'h-5 w-5 rotate-180']); ?>
                </button>
                <button
                    type="button"
                    @click="next(); pauseAutoplay()"
                    class="hero-slider__control"
                    aria-label="Next slide"
                >
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-right', 'class' => 'h-5 w-5']); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Progress bar -->
    <div class="hero-slider__progress" x-show="slides.length > 1" x-cloak>
        <div
            class="hero-slider__progress-bar"
            :style="'width:' + progress + '%'"
            aria-hidden="true"
        ></div>
    </div>
</section>

<script>
function heroSlider(slides) {
    return {
        slides,
        current: 0,
        progress: 0,
        _timer: null,
        _progTimer: null,
        _paused: false,
        _resumeTimer: null,
        _touchStartX: null,
        interval: 6000,

        goTo(i) { this.current = (i + this.slides.length) % this.slides.length; this.progress = 0; },
        next()   { this.goTo(this.current + 1); },
        prev()   { this.goTo(this.current - 1); },

        startAutoplay() {
            if (this.slides.length <= 1) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            this._runAutoplay();
        },
        _runAutoplay() {
            clearInterval(this._timer);
            clearInterval(this._progTimer);
            this.progress = 0;
            const step = 100 / (this.interval / 100);
            this._progTimer = setInterval(() => { if (!this._paused) this.progress = Math.min(this.progress + step, 100); }, 100);
            this._timer = setInterval(() => {
                if (this._paused) return;
                this.next();
                this.progress = 0;
            }, this.interval);
        },
        holdAutoplay() {
            this._paused = true;
        },
        resumeAutoplay() {
            clearTimeout(this._resumeTimer);
            this._paused = false;
        },
        pauseAutoplay() {
            this._paused = true;
            clearTimeout(this._resumeTimer);
            this._resumeTimer = setTimeout(() => { this._paused = false; }, 8000);
        },
        touchStart(event) {
            this._touchStartX = event.changedTouches && event.changedTouches[0]
                ? event.changedTouches[0].clientX
                : null;
        },
        touchEnd(event) {
            if (this._touchStartX === null || !event.changedTouches || !event.changedTouches[0]) return;
            const delta = event.changedTouches[0].clientX - this._touchStartX;
            this._touchStartX = null;

            if (Math.abs(delta) < 48) return;
            delta < 0 ? this.next() : this.prev();
            this.pauseAutoplay();
        },
    };
}
</script>
<?php endif; ?>

<!-- Trust / stats band (count-up) -->
<?php if ($statItems !== []) : ?>
<section class="stats-band" aria-labelledby="home-stats-heading">
    <div class="container-page">
        <div class="stats-band__layout">
            <div class="stats-band__intro" data-reveal>
                <p class="stats-band__eyebrow">Delivery at a glance</p>
                <h2 id="home-stats-heading">Built for measurable, accountable execution</h2>
                <p>A focused operating model delivering integrated solutions across Engineering, Energy, Procurement, Fire Safety & HSE, ICT, and Agro-support services.</p>
            </div>
            <dl class="stats-band__grid">
                <?php foreach ($statItems as $statIndex => $stat) : ?>
                    <div class="stats-card" data-reveal>
                        <dd
                            class="stats-card__value"
                            data-countup="<?php echo $this->escape((string) ($stat['value'] ?? '0')); ?>"
                            data-countup-suffix="<?php echo $this->escape((string) ($stat['suffix'] ?? '')); ?>"
                        ><?php echo $this->escape((string) ($stat['value'] ?? '0') . ($stat['suffix'] ?? '')); ?></dd>
                        <dt class="stats-card__label"><?php echo $this->escape((string) ($stat['label'] ?? '')); ?></dt>
                        <span class="stats-card__index" aria-hidden="true"><?php echo str_pad((string) ($statIndex + 1), 2, '0', STR_PAD_LEFT); ?></span>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Services overview -->
<?php if ($servicesIntro !== [] && $services !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="max-w-3xl" data-reveal>
            <?php if (!empty($servicesIntro['eyebrow'])) : ?>
                <p class="eyebrow"><?php echo $this->escape((string) $servicesIntro['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($servicesIntro['heading'] ?? '')); ?></h2>
            <?php if (!empty($servicesIntro['text'])) : ?>
                <p class="section-lead"><?php echo $this->escape((string) $servicesIntro['text']); ?></p>
            <?php endif; ?>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <article class="card-interactive service-card flex flex-col" data-reveal>
                    <?php if (!empty($service['image'])) : ?>
                        <img
                            src="<?php echo $this->escape((string) $service['image']); ?>"
                            alt="<?php echo $this->escape((string) $service['title']); ?>"
                            class="service-card__image"
                            loading="lazy"
                        >
                    <?php endif; ?>
                    <div class="card-body flex flex-1 flex-col">
                        <h3 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) $service['title']); ?></h3>
                        <p class="mt-3 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $service['summary']); ?></p>
                        <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="btn-ghost mt-5">
                            <?php echo $this->escape((string) ($servicesIntro['item_cta_label'] ?? 'Explore')); ?>
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Why choose us -->
<?php if ($whyChooseUs !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page grid gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div data-reveal>
            <?php if (!empty($whyChooseUs['eyebrow'])) : ?>
                <p class="eyebrow-success"><?php echo $this->escape((string) $whyChooseUs['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($whyChooseUs['heading'] ?? '')); ?></h2>
            <?php if (!empty($whyChooseUs['text'])) : ?>
                <p class="section-lead"><?php echo $this->escape((string) $whyChooseUs['text']); ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($whyChooseUs['benefits']) && is_array($whyChooseUs['benefits'])) : ?>
            <div class="grid gap-4 sm:grid-cols-2">
                <?php foreach ($whyChooseUs['benefits'] as $benefit) : ?>
                    <div class="flex items-start gap-4 rounded-lg bg-white p-5 shadow-card" data-reveal>
                        <span class="icon-tile-success">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-6 w-6']); ?>
                        </span>
                        <p class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $benefit); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Industry expertise (sector self-identification) -->
<?php if ($services !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="max-w-3xl" data-reveal>
            <p class="eyebrow">Industry expertise</p>
            <h2 class="mt-3 section-heading">Find your sector, then go deep</h2>
            <p class="section-lead">Six integrated practices under one accountable partner. Pick your industry to explore tailored capabilities.</p>
        </div>
        <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <a
                    href="/services/<?php echo $this->escape((string) $service['slug']); ?>"
                    class="group flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-5 transition duration-base ease-brand hover:-translate-y-1 hover:border-desnky-primary hover:shadow-lg"
                    data-reveal
                >
                    <span class="icon-tile h-12 w-12">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => $iconFor((string) $service['slug']), 'class' => 'h-6 w-6']); ?>
                    </span>
                    <span class="flex-1">
                        <span class="block font-bold text-desnky-dark"><?php echo $this->escape((string) $service['title']); ?></span>
                    </span>
                    <span class="text-desnky-primary opacity-0 transition-opacity group-hover:opacity-100">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-5 w-5']); ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- HSE commitment -->
<?php if ($hseCommitment !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page grid gap-10 lg:grid-cols-[1fr_0.85fr] lg:items-center">
        <div data-reveal>
            <?php if (!empty($hseCommitment['eyebrow'])) : ?>
                <p class="eyebrow-success"><?php echo $this->escape((string) $hseCommitment['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($hseCommitment['heading'] ?? '')); ?></h2>
            <?php if (!empty($hseCommitment['text'])) : ?>
                <p class="section-lead"><?php echo $this->escape((string) $hseCommitment['text']); ?></p>
            <?php endif; ?>
            <?php if (!empty($hseCommitment['cta_label']) && !empty($hseCommitment['cta_url'])) : ?>
                <a href="<?php echo $this->escape((string) $hseCommitment['cta_url']); ?>" class="btn-ghost mt-6">
                    <?php echo $this->escape((string) $hseCommitment['cta_label']); ?>
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                </a>
            <?php endif; ?>
        </div>
        <img
            src="/assets/images/HSE-commitment.png"
            alt="<?php echo $this->escape((string) ($hseCommitment['image_alt'] ?? 'DESNKY team demonstrating its health, safety and environmental commitment')); ?>"
            class="h-72 w-full rounded-lg object-cover shadow-card lg:h-80"
            loading="lazy"
            width="843"
            height="1264"
        >
    </div>
</section>
<?php endif; ?>

<!-- Featured projects -->
<?php if ($projectsIntro !== [] && $projects !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal>
            <div>
                <?php if (!empty($projectsIntro['eyebrow'])) : ?>
                    <p class="eyebrow"><?php echo $this->escape((string) $projectsIntro['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($projectsIntro['heading'] ?? '')); ?></h2>
            </div>
            <?php if (!empty($projectsIntro['cta_label']) && !empty($projectsIntro['cta_url'])) : ?>
                <a href="<?php echo $this->escape((string) $projectsIntro['cta_url']); ?>" class="btn-secondary">
                    <?php echo $this->escape((string) $projectsIntro['cta_label']); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            <?php foreach ($projects as $project) : ?>
                <article class="card-interactive flex flex-col" data-reveal>
                    <?php if (!empty($project['image'])) : ?>
                        <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="block overflow-hidden">
                            <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape((string) $project['title']); ?>" class="h-52 w-full object-cover transition-transform duration-slow hover:scale-105" loading="lazy">
                        </a>
                    <?php endif; ?>
                    <div class="card-body flex flex-1 flex-col">
                        <?php if (!empty($project['category'])) : ?>
                            <span class="badge-brand mb-3 self-start"><?php echo $this->escape((string) $project['category']); ?></span>
                        <?php endif; ?>
                        <h3 class="text-lg font-bold text-desnky-dark">
                            <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="hover:text-desnky-primary"><?php echo $this->escape((string) $project['title']); ?></a>
                        </h3>
                        <p class="mt-2 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $project['summary']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest insights -->
<?php if ($blogPosts !== []) : ?>
<section class="home-insights" aria-labelledby="home-insights-heading">
    <div class="container-page">
        <div class="home-insights__header" data-reveal>
            <div>
                <p class="eyebrow">From our journal</p>
                <h2 id="home-insights-heading" class="mt-2 section-heading">Ideas for safer, smarter operations</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-desnky-muted sm:text-base">Practical perspectives across engineering, energy, procurement, HSE and business operations.</p>
            </div>
            <a href="/blog" class="btn-secondary home-insights__all">
                View all articles
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
            </a>
        </div>

        <div class="home-insights__grid">
            <?php foreach (array_slice($blogPosts, 0, 3) as $post) : ?>
                <?php
                $postUrl = '/blog/' . rawurlencode((string) ($post['slug'] ?? ''));
                $publishedAt = (string) ($post['published_at'] ?? '');
                $timestamp = $publishedAt !== '' ? strtotime($publishedAt) : false;
                ?>
                <article class="home-insight-card group" data-reveal>
                    <a href="<?php echo $this->escape($postUrl); ?>" class="home-insight-card__media" aria-label="Read <?php echo $this->escape((string) ($post['title'] ?? 'article')); ?>">
                        <?php if (!empty($post['image'])) : ?>
                            <img src="<?php echo $this->escape((string) $post['image']); ?>" alt="<?php echo $this->escape((string) ($post['image_alt'] ?? $post['title'] ?? '')); ?>" loading="lazy" width="720" height="450">
                        <?php else : ?>
                            <span class="home-insight-card__placeholder" aria-hidden="true">Insight</span>
                        <?php endif; ?>
                    </a>
                    <div class="home-insight-card__body">
                        <div class="home-insight-card__meta">
                            <?php if (!empty($post['category_name'])) : ?><span><?php echo $this->escape((string) $post['category_name']); ?></span><?php endif; ?>
                            <?php if ($timestamp !== false) : ?><time datetime="<?php echo date('Y-m-d', $timestamp); ?>"><?php echo date('M j, Y', $timestamp); ?></time><?php endif; ?>
                            <span><?php echo (int) ($post['reading_time'] ?? 1); ?> min read</span>
                        </div>
                        <h3><a href="<?php echo $this->escape($postUrl); ?>"><?php echo $this->escape((string) ($post['title'] ?? '')); ?></a></h3>
                        <?php if (!empty($post['excerpt'])) : ?><p><?php echo $this->escape((string) $post['excerpt']); ?></p><?php endif; ?>
                        <a href="<?php echo $this->escape($postUrl); ?>" class="home-insight-card__link">
                            Read article
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Testimonials (defensive — only when CMS provides them) -->
<?php if ($testimonials !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="max-w-3xl" data-reveal>
            <p class="eyebrow">What clients say</p>
            <h2 class="mt-3 section-heading">Trusted across industrial Nigeria</h2>
        </div>
        <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($testimonials as $testimonial) : ?>
                <figure class="flex h-full flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-card" data-reveal>
                    <span class="text-desnky-primary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'quote', 'class' => 'h-8 w-8']); ?></span>
                    <blockquote class="mt-4 flex-1 text-base leading-7 text-desnky-ink">"<?php echo $this->escape((string) ($testimonial['quote'] ?? '')); ?>"</blockquote>
                    <figcaption class="mt-5 border-t border-gray-100 pt-4">
                        <span class="block font-bold text-desnky-dark"><?php echo $this->escape((string) ($testimonial['name'] ?? '')); ?></span>
                        <span class="block text-sm text-desnky-muted"><?php echo $this->escape(trim((string) ($testimonial['role'] ?? '') . (!empty($testimonial['company']) ? ', ' . $testimonial['company'] : ''), ', ')); ?></span>
                    </figcaption>
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Client showcase -->
<?php if (!empty($clients)) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="mx-auto max-w-3xl text-center" data-reveal>
            <p class="eyebrow">Client confidence</p>
            <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($clientsSection['heading'] ?? 'Trusted by leading organisations')); ?></h2>
        </div>
        <?php
        $clientItems = array_values($clients);
        $clientRows = [[], []];
        foreach ($clientItems as $index => $client) {
            $clientRows[$index % 2][] = $client;
        }
        if ($clientRows[1] === []) {
            $clientRows[1] = $clientRows[0];
        }
        $renderClientCard = function ($client, bool $duplicate = false): void {
            $clientName = is_array($client) ? (string) ($client['name'] ?? '') : (string) $client;
            $clientLogo = is_array($client) ? (string) ($client['logo'] ?? '') : '';
            $clientUrl  = is_array($client) ? (string) ($client['website_url'] ?? '') : '';
            $hiddenAttrs = $duplicate ? ' aria-hidden="true"' : '';
            $hiddenLinkAttrs = $duplicate ? ' aria-hidden="true" tabindex="-1"' : '';
            ?>
            <?php if ($clientUrl !== '') : ?>
            <a href="<?php echo $this->escape($clientUrl); ?>" target="_blank" rel="noopener noreferrer" class="trusted-client-card"<?php echo $hiddenLinkAttrs; ?>>
            <?php else : ?>
            <div class="trusted-client-card"<?php echo $hiddenAttrs; ?>>
            <?php endif; ?>
                <?php if ($clientLogo !== '') : ?>
                    <img src="<?php echo $this->escape($clientLogo); ?>" alt="<?php echo $this->escape($clientName); ?>" class="trusted-client-logo" loading="lazy">
                <?php else : ?>
                    <span class="trusted-client-name"><?php echo $this->escape($clientName); ?></span>
                <?php endif; ?>
            <?php if ($clientUrl !== '') : ?>
            </a>
            <?php else : ?>
            </div>
            <?php endif; ?>
            <?php
        };
        ?>
        <div class="trusted-carousel mt-10" aria-label="Trusted clients carousel" data-reveal>
            <div class="trusted-carousel__mobile">
                <div class="trusted-carousel__track">
                    <?php foreach ($clientItems as $client) : ?>
                        <?php $renderClientCard($client); ?>
                    <?php endforeach; ?>
                    <?php foreach ($clientItems as $client) : ?>
                        <?php $renderClientCard($client, true); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="trusted-carousel__desktop">
                <?php foreach ($clientRows as $rowIndex => $row) : ?>
                    <div class="trusted-carousel__track <?php echo $rowIndex === 1 ? 'trusted-carousel__track--reverse' : ''; ?>">
                        <?php foreach ($row as $client) : ?>
                            <?php $renderClientCard($client); ?>
                        <?php endforeach; ?>
                        <?php foreach ($row as $client) : ?>
                            <?php $renderClientCard($client, true); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php if (false) : ?>
        <div class="rounded-lg bg-white/5 p-6 ring-1 ring-white/10 sm:p-8" data-reveal>
            <h3 class="text-lg font-bold text-white"><?php echo $this->escape((string) ($cta['newsletter_heading'] ?? 'Subscribe to industry insights')); ?></h3>
            <p class="mt-2 text-sm text-gray-300">Project trends, HSE guidance and procurement tips — straight to your inbox.</p>
            <form class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]" action="/newsletter" method="POST" data-newsletter-form>
                <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($csrf_token ?? '')); ?>">
                <label class="sr-only" for="newsletter_email"><?php echo $this->escape((string) ($cta['email_label'] ?? 'Email address')); ?></label>
                <input
                    id="newsletter_email"
                    name="email"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="<?php echo $this->escape((string) ($cta['email_placeholder'] ?? 'you@company.com')); ?>"
                    class="w-full rounded-md border-0 bg-white/10 px-4 py-2.5 text-sm text-white placeholder:text-gray-400 ring-1 ring-inset ring-white/15 focus:ring-2 focus:ring-inset focus:ring-white"
                >
                <button type="submit" class="btn-on-dark whitespace-nowrap">
                    <?php echo $this->escape((string) ($cta['newsletter_button_label'] ?? 'Subscribe')); ?>
                </button>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>
