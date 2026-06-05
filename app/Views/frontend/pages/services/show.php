<?php
$service = $service ?? [];
$detail = $detail ?? [];
$relatedServices = $relatedServices ?? [];

$slug = (string) ($service['slug'] ?? '');
$sectorIcons = [
    'engineering' => 'cog',
    'energy-solutions' => 'bolt',
    'procurement' => 'truck',
    'hse-safety' => 'shield-check',
    'ict-solutions' => 'server',
    'agro-food-processing' => 'leaf',
];
$icon = $sectorIcons[$slug] ?? 'sparkles';
$iconFor = static fn (string $s): string => $sectorIcons[$s] ?? 'sparkles';

$features = is_array($service['features'] ?? null) ? $service['features'] : [];
$processSteps = is_array($service['process'] ?? null) ? $service['process'] : [];
$benefits = is_array($service['benefits'] ?? null) ? $service['benefits'] : [];
$faqs = is_array($service['faqs'] ?? null) ? $service['faqs'] : [];

// In-page section anchors (only those with content).
$anchors = [];
if (!empty($service['overview']) || $features !== []) {
    $anchors[] = ['id' => 'overview', 'label' => 'Overview'];
}
if ($processSteps !== []) {
    $anchors[] = ['id' => 'process', 'label' => 'Process'];
}
if ($benefits !== []) {
    $anchors[] = ['id' => 'benefits', 'label' => 'Benefits'];
}
if ($faqs !== []) {
    $anchors[] = ['id' => 'faq', 'label' => 'FAQ'];
}
?>

<!-- Sector hero -->
<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <?php if (!empty($service['image'])) : ?>
        <img src="<?php echo $this->escape((string) $service['image']); ?>" alt="<?php echo $this->escape((string) $service['title']); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-30" loading="eager" fetchpriority="high">
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-desnky-dark/85 via-desnky-dark/80 to-desnky-dark"></div>
    <div class="container-page py-16 sm:py-20 lg:py-24">
        <nav class="text-sm text-gray-300" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? 'Home')); ?></a></li>
                <li class="text-gray-500" aria-hidden="true">/</li>
                <li><a href="/services" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_services_label'] ?? 'Services')); ?></a></li>
                <li class="text-gray-500" aria-hidden="true">/</li>
                <li><span class="text-desnky-primary-200" aria-current="page"><?php echo $this->escape((string) $service['title']); ?></span></li>
            </ol>
        </nav>
        <div class="mt-8 max-w-3xl">
            <span class="inline-flex h-14 w-14 items-center justify-center rounded-lg bg-white/10 ring-1 ring-white/20">
                <?php echo $this->partial('frontend/partials/icon', ['name' => $icon, 'class' => 'h-7 w-7']); ?>
            </span>
            <h1 class="mt-6 text-4xl font-bold leading-tight sm:text-5xl"><?php echo $this->escape((string) $service['title']); ?></h1>
            <p class="mt-5 text-lg leading-8 text-gray-200"><?php echo $this->escape((string) $service['summary']); ?></p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="<?php echo $this->escape((string) ($detail['primary_cta_url'] ?? '/contact?service=' . $slug)); ?>" class="btn-on-dark">
                    <?php echo $this->escape((string) ($detail['primary_cta_label'] ?? 'Discuss your project')); ?>
                </a>
                <a href="/projects" class="btn-secondary-on-dark">View related work</a>
            </div>
        </div>
    </div>
</section>

<!-- Sticky in-page sub-nav (desktop) -->
<?php if (count($anchors) > 1) : ?>
<div class="sticky top-20 z-30 hidden border-b border-gray-200 bg-white/95 backdrop-blur lg:block">
    <div class="container-page">
        <nav class="flex gap-6 py-3 text-sm font-semibold" aria-label="On this page">
            <?php foreach ($anchors as $anchor) : ?>
                <a href="#<?php echo $this->escape($anchor['id']); ?>" class="text-desnky-muted hover:text-desnky-primary"><?php echo $this->escape($anchor['label']); ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</div>
<?php endif; ?>

<section class="section-band">
    <div class="container-page grid gap-12 lg:grid-cols-[1fr_22rem]">
        <div>
            <div id="overview" class="scroll-mt-28">
                <h2 class="section-heading"><?php echo $this->escape((string) ($detail['features_heading'] ?? 'What we deliver')); ?></h2>
                <?php if (!empty($service['overview'])) : ?>
                    <div class="prose-desnky mt-6"><?php echo nl2br($this->escape((string) $service['overview'])); ?></div>
                <?php endif; ?>
                <?php if ($features !== []) : ?>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <?php foreach ($features as $feature) : ?>
                            <div class="flex items-start gap-3 rounded-lg border border-gray-200 bg-white p-5 shadow-card" data-reveal>
                                <span class="icon-tile-success h-9 w-9">
                                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-5 w-5']); ?>
                                </span>
                                <p class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $feature); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($processSteps !== []) : ?>
                <div id="process" class="mt-14 scroll-mt-28">
                    <h2 class="section-heading"><?php echo $this->escape((string) ($detail['process_heading'] ?? 'Our approach')); ?></h2>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <?php foreach ($processSteps as $index => $step) : ?>
                            <div class="rounded-lg bg-desnky-surface p-6" data-reveal>
                                <span class="text-2xl font-bold text-desnky-primary-200"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                                <p class="mt-3 font-semibold text-desnky-dark"><?php echo $this->escape((string) $step); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($faqs !== []) : ?>
                <div id="faq" class="mt-14 scroll-mt-28">
                    <h2 class="section-heading">Frequently asked questions</h2>
                    <div class="mt-8 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white" x-data="{ open: 0 }">
                        <?php foreach ($faqs as $i => $faq) : ?>
                            <?php if (!is_array($faq)) {
                                continue;
                            } ?>
                            <div>
                                <h3>
                                    <button type="button" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-desnky-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-desnky-primary" @click="open = open === <?php echo $i; ?> ? null : <?php echo $i; ?>" :aria-expanded="(open === <?php echo $i; ?>).toString()">
                                        <?php echo $this->escape((string) ($faq['question'] ?? '')); ?>
                                        <span :class="open === <?php echo $i; ?> && 'rotate-180'" class="shrink-0 text-desnky-primary transition-transform"><?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-5 w-5']); ?></span>
                                    </button>
                                </h3>
                                <div x-show="open === <?php echo $i; ?>" x-collapse x-cloak>
                                    <p class="px-6 pb-5 text-sm leading-7 text-desnky-muted"><?php echo $this->escape((string) ($faq['answer'] ?? '')); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Benefits / CTA sidebar -->
        <aside class="lg:sticky lg:top-32 lg:self-start" id="benefits">
            <div class="scroll-mt-28 rounded-lg border border-gray-200 bg-desnky-surface p-6 shadow-card">
                <?php if ($benefits !== []) : ?>
                    <h2 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($detail['benefits_heading'] ?? 'Why choose us')); ?></h2>
                    <ul class="mt-5 space-y-3 text-sm leading-6 text-desnky-ink">
                        <?php foreach ($benefits as $benefit) : ?>
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-desnky-secondary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'check-circle', 'class' => 'h-5 w-5']); ?></span>
                                <span><?php echo $this->escape((string) $benefit); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <div class="mt-6 rounded-md bg-desnky-dark p-5 text-white">
                    <p class="font-semibold">Ready to start?</p>
                    <p class="mt-1 text-sm text-gray-300">Get a tailored quote for your <?php echo $this->escape((string) $service['title']); ?> requirement.</p>
                    <a href="/contact?service=<?php echo $this->escape($slug); ?>" class="btn-on-dark mt-4 w-full"><?php echo $this->escape((string) ($detail['secondary_cta_label'] ?? 'Request a Quote')); ?></a>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php if ($relatedServices !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h2 class="section-heading"><?php echo $this->escape((string) ($detail['related_heading'] ?? 'Related services')); ?></h2>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php foreach (array_slice($relatedServices, 0, 3) as $related) : ?>
                <a href="/services/<?php echo $this->escape((string) $related['slug']); ?>" class="card-interactive block" data-reveal>
                    <div class="card-body">
                        <span class="icon-tile"><?php echo $this->partial('frontend/partials/icon', ['name' => $iconFor((string) $related['slug']), 'class' => 'h-6 w-6']); ?></span>
                        <h3 class="mt-5 text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) $related['title']); ?></h3>
                        <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $related['summary']); ?></p>
                        <span class="btn-ghost mt-4">Explore <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
