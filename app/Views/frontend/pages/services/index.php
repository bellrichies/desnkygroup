<?php
$hero = $hero ?? [];
$listing = $listing ?? [];
$services = $services ?? [];

$sectorIcons = [
    'engineering' => 'cog',
    'energy-solutions' => 'bolt',
    'procurement' => 'truck',
    'hse-safety' => 'shield-check',
    'ict-solutions' => 'server',
    'agro-food-processing' => 'leaf',
];
$iconFor = static fn (string $slug): string => $sectorIcons[$slug] ?? 'sparkles';

$process = [
    ['title' => 'Enquiry', 'text' => 'Share your requirement; we respond within one business day.'],
    ['title' => 'Scope', 'text' => 'We define deliverables, timeline, HSE plan and a clear quotation.'],
    ['title' => 'Deliver', 'text' => 'Qualified teams execute to standard, on schedule and safely.'],
    ['title' => 'Support', 'text' => 'Ongoing maintenance, supply and advisory keep you running.'],
];
?>

<?php echo $this->partial('frontend/partials/page-hero', [
    'hero' => $hero,
    'title' => $title ?? '',
    'fallbackImage' => $page['featured_image'] ?? '',
    'breadcrumbs' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Services'],
    ],
]); ?>

<?php if ($services !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="mb-8 flex flex-wrap gap-2.5" aria-label="<?php echo $this->escape((string) ($listing['filters_label'] ?? 'Service filters')); ?>">
            <a href="/services" class="chip chip-active"><?php echo $this->escape((string) ($listing['all_services_label'] ?? 'All services')); ?></a>
            <?php foreach ($services as $service) : ?>
                <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="chip"><?php echo $this->escape((string) $service['title']); ?></a>
            <?php endforeach; ?>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <article class="card-interactive flex flex-col" data-reveal>
                    <?php if (!empty($service['image'])) : ?>
                        <img src="<?php echo $this->escape((string) $service['image']); ?>" alt="<?php echo $this->escape((string) $service['title']); ?>" class="h-48 w-full object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="card-body flex flex-1 flex-col">
                        <span class="icon-tile">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => $iconFor((string) $service['slug']), 'class' => 'h-6 w-6']); ?>
                        </span>
                        <h2 class="mt-5 text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) $service['title']); ?></h2>
                        <p class="mt-3 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $service['summary']); ?></p>
                        <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="btn-ghost mt-5">
                            <?php echo $this->escape((string) ($listing['card_cta_label'] ?? 'Explore')); ?>
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Process overview -->
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="max-w-3xl" data-reveal>
            <p class="eyebrow">How we work</p>
            <h2 class="mt-3 section-heading">One accountable partner, end to end</h2>
            <p class="section-lead">A consistent delivery model across all six sectors — predictable, safe and measurable.</p>
        </div>
        <ol class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($process as $index => $step) : ?>
                <li class="relative rounded-lg border border-gray-200 bg-white p-6 shadow-card" data-reveal>
                    <span class="text-2xl font-bold text-desnky-primary-200"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                    <h3 class="mt-3 text-lg font-bold text-desnky-dark"><?php echo $this->escape($step['title']); ?></h3>
                    <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($step['text']); ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<!-- CTA band -->
<section class="bg-desnky-dark text-white">
    <div class="container-page section-band flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold sm:text-4xl">Not sure which service you need?</h2>
            <p class="mt-3 text-lg text-gray-200">Tell us your challenge and we'll route you to the right team.</p>
        </div>
        <a href="/contact" class="btn-on-dark">Discuss your requirements</a>
    </div>
</section>
