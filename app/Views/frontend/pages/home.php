<?php
$hero = $hero ?? [];
$servicesIntro = $servicesIntro ?? [];
$whyChooseUs = $whyChooseUs ?? [];
$hseCommitment = $hseCommitment ?? [];
$projectsIntro = $projectsIntro ?? [];
$clientsSection = $clientsSection ?? [];
$cta = $cta ?? [];
$services = $services ?? [];
$projects = $projects ?? [];
$clients = $clientsSection['items'] ?? [];
$testimonials = $testimonials ?? [];

// CMS-overridable stats band; sensible defaults keep the band populated.
$stats = $stats ?? [];
$statItems = $stats['items'] ?? [
    ['value' => '15', 'suffix' => '+', 'label' => 'Years of operation'],
    ['value' => '200', 'suffix' => '+', 'label' => 'Projects delivered'],
    ['value' => '6', 'suffix' => '', 'label' => 'Industrial sectors'],
    ['value' => '100', 'suffix' => '%', 'label' => 'HSE commitment'],
];

// Map service slugs to curated sector icons for consistent iconography.
$sectorIcons = [
    'engineering' => 'cog',
    'energy-solutions' => 'bolt',
    'procurement' => 'truck',
    'hse-safety' => 'shield-check',
    'ict-solutions' => 'server',
    'agro-food-processing' => 'leaf',
];
$iconFor = static function (string $slug) use ($sectorIcons): string {
    return $sectorIcons[$slug] ?? 'sparkles';
};
?>

<?php if ($hero !== []) : ?>
<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <?php if (!empty($hero['image'])) : ?>
        <img
            src="<?php echo $this->escape((string) $hero['image']); ?>"
            alt="<?php echo $this->escape((string) ($hero['image_alt'] ?? $hero['heading'] ?? '')); ?>"
            class="absolute inset-0 -z-10 h-full w-full object-cover opacity-40"
            loading="eager"
            fetchpriority="high"
        >
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-desnky-dark via-desnky-dark/85 to-desnky-dark/70"></div>
    <div class="container-page grid min-h-[calc(100vh-5rem)] content-center py-20 sm:py-24">
        <div class="max-w-4xl">
            <?php if (!empty($hero['eyebrow'])) : ?>
                <p class="eyebrow-on-dark"><?php echo $this->escape((string) $hero['eyebrow']); ?></p>
            <?php endif; ?>
            <h1 class="mt-5 max-w-5xl text-4xl font-bold leading-tight sm:text-6xl">
                <?php echo $this->escape((string) ($hero['heading'] ?? '')); ?>
            </h1>
            <?php if (!empty($hero['text'])) : ?>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-200"><?php echo $this->escape((string) $hero['text']); ?></p>
            <?php endif; ?>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <?php if (!empty($hero['primary_cta_label']) && !empty($hero['primary_cta_url'])) : ?>
                    <a href="<?php echo $this->escape((string) $hero['primary_cta_url']); ?>" class="btn-on-dark" data-analytics-event="hero_quote">
                        <?php echo $this->escape((string) $hero['primary_cta_label']); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($hero['secondary_cta_label']) && !empty($hero['secondary_cta_url'])) : ?>
                    <a href="<?php echo $this->escape((string) $hero['secondary_cta_url']); ?>" class="btn-secondary-on-dark">
                        <?php echo $this->escape((string) $hero['secondary_cta_label']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Trust / stats band (count-up) -->
<?php if ($statItems !== []) : ?>
<section class="border-b border-gray-200 bg-white">
    <div class="container-page py-10">
        <dl class="grid grid-cols-2 gap-6 lg:grid-cols-4">
            <?php foreach ($statItems as $stat) : ?>
                <div class="text-center" data-reveal>
                    <dd
                        class="text-4xl font-bold text-desnky-dark"
                        data-countup="<?php echo $this->escape((string) ($stat['value'] ?? '0')); ?>"
                        data-countup-suffix="<?php echo $this->escape((string) ($stat['suffix'] ?? '')); ?>"
                    ><?php echo $this->escape((string) ($stat['value'] ?? '0') . ($stat['suffix'] ?? '')); ?></dd>
                    <dt class="mt-2 text-sm font-medium text-desnky-muted"><?php echo $this->escape((string) ($stat['label'] ?? '')); ?></dt>
                </div>
            <?php endforeach; ?>
        </dl>
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
                <article class="card-interactive flex flex-col" data-reveal>
                    <?php if (!empty($service['image'])) : ?>
                        <img
                            src="<?php echo $this->escape((string) $service['image']); ?>"
                            alt="<?php echo $this->escape((string) $service['title']); ?>"
                            class="h-48 w-full object-cover"
                            loading="lazy"
                        >
                    <?php endif; ?>
                    <div class="card-body flex flex-1 flex-col">
                        <span class="icon-tile">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => $iconFor((string) $service['slug']), 'class' => 'h-6 w-6']); ?>
                        </span>
                        <h3 class="mt-5 text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) $service['title']); ?></h3>
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
        <?php if (!empty($hseCommitment['image'])) : ?>
            <img
                src="<?php echo $this->escape((string) $hseCommitment['image']); ?>"
                alt="<?php echo $this->escape((string) ($hseCommitment['image_alt'] ?? $hseCommitment['heading'] ?? '')); ?>"
                class="h-72 w-full rounded-lg object-cover shadow-card lg:h-80"
                loading="lazy"
            >
        <?php endif; ?>
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
<?php if ($clientsSection !== [] && is_array($clients) && $clients !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <h2 class="text-center text-2xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($clientsSection['heading'] ?? 'Trusted by leading organisations')); ?></h2>
        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            <?php foreach ($clients as $client) : ?>
                <div class="flex items-center justify-center rounded-lg border border-gray-200 bg-white px-5 py-6 text-center text-sm font-semibold text-desnky-muted transition-colors hover:text-desnky-dark">
                    <?php echo $this->escape((string) $client); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Conversion + newsletter band -->
<?php if ($cta !== []) : ?>
<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <div class="container-page section-band grid gap-10 lg:grid-cols-2 lg:items-center">
        <div data-reveal>
            <p class="eyebrow-on-dark">Let's build together</p>
            <h2 class="mt-3 text-3xl font-bold sm:text-4xl"><?php echo $this->escape((string) ($cta['heading'] ?? '')); ?></h2>
            <?php if (!empty($cta['text'])) : ?>
                <p class="mt-4 max-w-xl text-lg text-gray-200"><?php echo $this->escape((string) $cta['text']); ?></p>
            <?php endif; ?>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="/contact" class="btn-on-dark" data-analytics-event="cta_quote">Request a Quote</a>
                <?php if (!empty($cta['contact_label']) && !empty($cta['contact_url'])) : ?>
                    <a href="<?php echo $this->escape((string) $cta['contact_url']); ?>" class="btn-secondary-on-dark"><?php echo $this->escape((string) $cta['contact_label']); ?></a>
                <?php endif; ?>
            </div>
        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('[data-newsletter-form]', { endpoint: '/newsletter', resetOnSuccess: true });
});
</script>
