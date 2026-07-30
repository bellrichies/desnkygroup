<?php
$service          ??= [];
$heroSlides       ??= [];
$heroManaged      ??= false;
$detail           ??= [];
$relatedServices  ??= [];
$featuredProjects ??= [];
$trustedClients   ??= [];
$documentation    ??= $service['documentation'] ?? null;

$slug = (string) ($service['slug'] ?? '');

$sectorIcons = [
    'engineering'          => 'cog',
    'energy-solutions'     => 'bolt',
    'procurement'          => 'truck',
    'hse-safety'           => 'shield-check',
    'ict-solutions'        => 'server',
    'agro-food-processing' => 'leaf',
];

$serviceThemes = [
    'engineering' => ['accent' => '#6c3483', 'accent2' => '#008000'],
    'energy-solutions' => ['accent' => '#008000', 'accent2' => '#6c3483'],
    'procurement' => ['accent' => '#1d1228', 'accent2' => '#6c3483'],
    'hse-safety' => ['accent' => '#008000', 'accent2' => '#1d1228'],
    'ict-solutions' => ['accent' => '#6c3483', 'accent2' => '#087ea4'],
    'agro-food-processing' => ['accent' => '#008000', 'accent2' => '#6c3483'],
];

$theme = $serviceThemes[$slug] ?? ['accent' => '#6c3483', 'accent2' => '#008000'];
$icon = $sectorIcons[$slug] ?? 'sparkles';
$iconFor = static fn (string $s): string => $sectorIcons[$s] ?? 'sparkles';

$legacyFeatures = \is_array($service['features'] ?? null) ? $service['features'] : [];
$legacyProcess = \is_array($service['process'] ?? null) ? $service['process'] : [];
$legacyBenefits = \is_array($service['key_benefits'] ?? null)
    ? $service['key_benefits']
    : (\is_array($service['benefits'] ?? null) ? $service['benefits'] : []);
$legacyFaqs = \is_array($service['faqs'] ?? null) ? $service['faqs'] : [];

if ($documentation === null) {
    $documentation = [
        'title' => (string) ($service['title'] ?? ''),
        'intro_heading' => (string) ($service['summary'] ?? ''),
        'intro_blocks' => !empty($service['overview'])
            ? [['type' => 'paragraph', 'text' => (string) $service['overview']]]
            : [],
        'sections' => array_values(array_filter([
            $legacyFeatures !== [] ? [
                'title' => (string) ($detail['features_heading'] ?? 'What we deliver'),
                'id' => 'what-we-deliver',
                'blocks' => [],
                'children' => array_map(static fn (string $feature): array => [
                    'title' => $feature,
                    'id' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $feature) ?: 'feature'),
                    'blocks' => [],
                    'children' => [],
                ], $legacyFeatures),
            ] : null,
            $legacyProcess !== [] ? [
                'title' => (string) ($detail['process_heading'] ?? 'Our process'),
                'id' => 'our-process',
                'blocks' => [['type' => 'paragraph', 'text' => 'A clear, accountable process from scoping to handover.']],
                'children' => array_map(static fn (string $step): array => [
                    'title' => $step,
                    'id' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $step) ?: 'step'),
                    'blocks' => [],
                    'children' => [],
                ], $legacyProcess),
            ] : null,
            $legacyBenefits !== [] ? [
                'title' => (string) ($detail['benefits_heading'] ?? 'Why choose DESNKY?'),
                'id' => 'why-choose-desnky',
                'blocks' => [],
                'children' => array_map(static fn (string $benefit): array => [
                    'title' => $benefit,
                    'id' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $benefit) ?: 'benefit'),
                    'blocks' => [],
                    'children' => [],
                ], $legacyBenefits),
            ] : null,
            $legacyFaqs !== [] ? [
                'title' => 'Frequently Asked Questions',
                'id' => 'frequently-asked-questions',
                'blocks' => [],
                'children' => array_map(static fn (array $faq): array => [
                    'title' => (string) ($faq['question'] ?? ''),
                    'id' => strtolower(preg_replace('/[^a-z0-9]+/', '-', (string) ($faq['question'] ?? 'faq')) ?: 'faq'),
                    'blocks' => [['type' => 'paragraph', 'text' => (string) ($faq['answer'] ?? '')]],
                    'children' => [],
                ], $legacyFaqs),
            ] : null,
        ])),
        'faqs' => $legacyFaqs,
        'contact_section' => null,
        'primary_cta' => [
            'label' => (string) ($detail['primary_cta_label'] ?? 'Discuss your requirement'),
            'section_id' => null,
        ],
        'secondary_cta' => [
            'label' => (string) ($detail['secondary_cta_label'] ?? 'Explore our capabilities'),
            'section_id' => null,
        ],
        'final_primary_cta' => null,
        'final_secondary_cta' => null,
        'form_button' => '',
    ];
}

$sections = \is_array($documentation['sections'] ?? null) ? $documentation['sections'] : [];
$faqs = \is_array($documentation['faqs'] ?? null) ? $documentation['faqs'] : [];
$introBlocks = \is_array($documentation['intro_blocks'] ?? null) ? $documentation['intro_blocks'] : [];
$primaryCta = \is_array($documentation['primary_cta'] ?? null) ? $documentation['primary_cta'] : null;
$secondaryCta = \is_array($documentation['secondary_cta'] ?? null) ? $documentation['secondary_cta'] : null;
$finalPrimaryCta = \is_array($documentation['final_primary_cta'] ?? null) ? $documentation['final_primary_cta'] : null;
$finalSecondaryCta = \is_array($documentation['final_secondary_cta'] ?? null) ? $documentation['final_secondary_cta'] : null;
$contactSection = \is_array($documentation['contact_section'] ?? null) ? $documentation['contact_section'] : null;

$contactServiceParams = [
    'engineering' => 'engineering',
    'energy-solutions' => 'energy',
    'procurement' => 'procurement',
    'hse-safety' => 'hse',
    'ict-solutions' => 'ict',
    'agro-food-processing' => 'agro',
];
$contactParam = $contactServiceParams[$slug] ?? $slug;
$contactHref = '/contact?service=' . rawurlencode($contactParam) . '#contact-form';

$firstContentSection = $sections[0]['id'] ?? 'service-content';
$secondaryHref = static function (?array $cta) use ($contactHref, $firstContentSection): string {
    $label = strtolower((string) ($cta['label'] ?? ''));

    if (str_starts_with($label, 'explore')) {
        return '#' . $firstContentSection;
    }

    return $contactHref;
};

$plainText = static function (array $blocks, int $limit = 2): string {
    $parts = [];

    foreach ($blocks as $block) {
        if (($block['type'] ?? '') !== 'paragraph') {
            continue;
        }

        $parts[] = str_replace('**', '', (string) ($block['text'] ?? ''));

        if (count($parts) >= $limit) {
            break;
        }
    }

    return trim(implode(' ', $parts));
};

$renderInline = function (string $text): string {
    $html = $this->escape($text);

    return (string) preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $html);
};

$renderBlocks = function (array $blocks, string $variant = 'default') use ($renderInline): void {
    $paragraphClass = $variant === 'compact'
        ? 'mt-3 text-sm leading-6 text-desnky-muted'
        : 'mt-4 text-base leading-7 text-desnky-ink';
    $listClass = $variant === 'compact'
        ? 'mt-4 grid gap-2 text-sm leading-6 text-desnky-ink'
        : 'mt-5 grid gap-2.5 text-sm leading-6 text-desnky-ink sm:grid-cols-2';

    foreach ($blocks as $block) {
        $type = (string) ($block['type'] ?? '');

        if ($type === 'paragraph') : ?>
            <p class="<?php echo $paragraphClass; ?>"><?php echo $renderInline((string) ($block['text'] ?? '')); ?></p>
        <?php elseif ($type === 'heading') : ?>
            <h4 class="mt-6 text-base font-bold text-desnky-dark"><?php echo $renderInline((string) ($block['text'] ?? '')); ?></h4>
        <?php elseif ($type === 'list' && \is_array($block['items'] ?? null)) : ?>
            <ul class="<?php echo $listClass; ?>">
                <?php foreach ($block['items'] as $item) : ?>
                    <li class="service-list-item">
                        <span class="service-list-item__icon" aria-hidden="true">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-3.5 w-3.5']); ?>
                        </span>
                        <span><?php echo $renderInline((string) $item); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif;
    }
};

$sectionMatches = static function (array $section, string $pattern): bool {
    return preg_match($pattern, (string) ($section['title'] ?? '')) === 1;
};

$findSection = static function (string $pattern) use ($sections, $sectionMatches): ?array {
    foreach ($sections as $section) {
        if (\is_array($section) && $sectionMatches($section, $pattern)) {
            return $section;
        }
    }

    return null;
};

$capabilitySection = $findSection('/capabilities/i') ?? ($sections[0] ?? null);
$capabilityPills = [];
if (\is_array($capabilitySection) && \is_array($capabilitySection['children'] ?? null)) {
    foreach (array_slice($capabilitySection['children'], 0, 6) as $child) {
        if (\is_array($child) && !empty($child['title'])) {
            $capabilityPills[] = (string) $child['title'];
        }
    }
}
if ($capabilityPills === [] && $legacyFeatures !== []) {
    $capabilityPills = array_slice(array_map('strval', $legacyFeatures), 0, 6);
}

$faqSectionId = null;
$contactSectionId = $contactSection['id'] ?? null;
$finalCtaSectionId = $finalPrimaryCta['section_id'] ?? null;

foreach ($sections as $section) {
    if ((string) ($section['title'] ?? '') === 'Frequently Asked Questions') {
        $faqSectionId = (string) ($section['id'] ?? '');
        break;
    }
}

$isFinalCtaSection = static function (array $section) use ($finalCtaSectionId, $contactSectionId, $faqSectionId): bool {
    $id = (string) ($section['id'] ?? '');

    return $finalCtaSectionId !== null
        && $id === $finalCtaSectionId
        && $id !== $contactSectionId
        && $id !== $faqSectionId;
};

$mainSections = array_values(array_filter($sections, static function (array $section) use (
    $isFinalCtaSection,
    $contactSectionId,
    $faqSectionId
): bool {
    $id = (string) ($section['id'] ?? '');

    return $id !== $contactSectionId
        && $id !== $faqSectionId
        && !$isFinalCtaSection($section);
}));

$finalCtaSection = null;
foreach ($sections as $section) {
    if (\is_array($section) && $isFinalCtaSection($section)) {
        $finalCtaSection = $section;
        break;
    }
}

$navItems = [['id' => 'overview', 'label' => 'Overview']];
foreach ($mainSections as $section) {
    $label = (string) ($section['title'] ?? '');
    $label = preg_replace('/^(Our|Integrated)\s+/i', '', $label) ?: $label;
    $label = $label === 'Frequently Asked Questions' ? 'FAQs' : $label;
    $words = preg_split('/\s+/', $label) ?: [];
    if (count($words) > 3) {
        $label = implode(' ', array_slice($words, 0, 3));
    }

    $navItems[] = [
        'id' => (string) ($section['id'] ?? ''),
        'label' => $label,
    ];
}
if ($faqs !== []) {
    $navItems[] = ['id' => 'faqs', 'label' => 'FAQs'];
}
if ($contactSection !== null) {
    $navItems[] = ['id' => 'enquiry', 'label' => 'Enquiry'];
}
$navItems = array_slice(array_filter($navItems, static fn (array $item): bool => $item['id'] !== ''), 0, 9);

$redesignedNavItems = [
    'energy-solutions' => [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'renewable-and-backup-power', 'label' => 'Power systems'],
        ['id' => 'power-quality-and-storage', 'label' => 'Storage & testing'],
        ['id' => 'plant-engineering-and-maintenance', 'label' => 'Plant engineering'],
        ['id' => 'petroleum-and-logistics', 'label' => 'Fuel & logistics'],
        ['id' => 'lpg-and-natural-gas', 'label' => 'Gas infrastructure'],
        ['id' => 'our-service-delivery-process', 'label' => 'Our process'],
        ['id' => 'faqs', 'label' => 'FAQs'],
    ],
    'procurement' => [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'our-procurement-capabilities', 'label' => 'Capabilities'],
        ['id' => 'specialist-and-general-supply', 'label' => 'Supply range'],
        ['id' => 'local-and-international-sourcing', 'label' => 'Sourcing'],
        ['id' => 'our-procurement-process', 'label' => 'Our process'],
    ],
    'ict-solutions' => [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'our-ict-capabilities', 'label' => 'Infrastructure'],
        ['id' => 'digital-workflow-support', 'label' => 'Workflows'],
        ['id' => 'application-development', 'label' => 'Applications'],
        ['id' => 'our-ict-delivery-process', 'label' => 'Our process'],
    ],
    'agro-food-processing' => [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'our-agro-capabilities', 'label' => 'Capabilities'],
        ['id' => 'food-processing-support', 'label' => 'Processing'],
        ['id' => 'market-linkage-and-logistics', 'label' => 'Market linkage'],
        ['id' => 'our-agro-delivery-process', 'label' => 'Our process'],
    ],
    'hse-safety' => [
        ['id' => 'overview', 'label' => 'Overview'],
        ['id' => 'hse-support-and-ppe-supply', 'label' => 'HSE & PPE'],
        ['id' => 'fire-extinguishers', 'label' => 'Extinguishers'],
        ['id' => 'hydrant-and-sprinkler-systems', 'label' => 'Hydrant & sprinkler'],
        ['id' => 'clean-agent-fire-suppression', 'label' => 'Suppression'],
        ['id' => 'fire-alarm-systems', 'label' => 'Fire alarms'],
        ['id' => 'fire-safety-delivery-process', 'label' => 'Our process'],
    ],
];
if (isset($redesignedNavItems[$slug])) {
    $navItems = $redesignedNavItems[$slug];
}

$pageTitle = (string) ($service['title'] ?? '');
$introHeading = (string) (($documentation['intro_heading'] ?? '') ?: ($service['summary'] ?? ''));
$heroImage = (string) ($service['image'] ?? '');
if ($slug === 'energy-solutions') {
    $introHeading = 'Integrated power and energy infrastructure built for dependable operations';
    $introBlocks = [
        [
            'type' => 'paragraph',
            'text' => 'DESNKY Energy and Infrastructure Ltd. (CAC Registered) delivers integrated energy and infrastructure solutions that help organisations maintain reliable, efficient and sustainable power systems.',
        ],
        [
            'type' => 'paragraph',
            'text' => 'Our capability spans solar and inverter installations, UPS and battery systems, industrial stabilizers, Battery Energy Storage Systems (BESS), load bank testing and anchor lifeline systems, alongside power plant operation and maintenance, equipment procurement and turnkey power engineering.',
        ],
        [
            'type' => 'paragraph',
            'text' => 'We also coordinate petroleum product trading and distribution, LPG services, truck rental and natural gas infrastructure planning—giving clients one practical partner from requirement assessment and supply through installation, testing, maintenance and long-term development.',
        ],
        [
            'type' => 'list',
            'items' => [
                'Reliable power generation, backup and storage',
                'Integrated engineering, procurement and maintenance',
                'Coordinated petroleum, LPG and transport services',
                'Scalable infrastructure planned for long-term performance',
            ],
        ],
    ];
    $heroImage = '/assets/images/services/energy.png';

    foreach ($heroSlides as &$energyHeroSlide) {
        if (($energyHeroSlide['media_type'] ?? 'image') === 'image') {
            $energyHeroSlide['background_media'] = '/assets/images/services/energy.png';
        }
    }
    unset($energyHeroSlide);
}
if ($slug === 'hse-safety') {
    $introHeading = 'Complete fire-safety protection for people, assets and operations';
    $introBlocks = [
        [
            'type' => 'paragraph',
            'text' => 'DESNKY delivers integrated HSE and fire-safety services that help organisations prevent incidents, detect threats early and maintain dependable emergency-response systems.',
        ],
        [
            'type' => 'paragraph',
            'text' => 'From PPE and portable extinguishers to hydrants, sprinklers, clean-agent suppression and fire alarms, we coordinate supply, installation, testing and planned maintenance around the risks within your facility.',
        ],
        [
            'type' => 'list',
            'items' => [
                'Risk-informed equipment and system selection',
                'Professional installation and commissioning',
                'Routine servicing, testing and maintenance',
                'Clear documentation and ongoing readiness support',
            ],
        ],
    ];
    $heroImage = '/assets/images/services/hse_1.jpg';

    foreach ($heroSlides as &$hseHeroSlide) {
        if (($hseHeroSlide['media_type'] ?? 'image') === 'image') {
            $hseHeroSlide['background_media'] = '/assets/images/services/hse_1.jpg';
        }
    }
    unset($hseHeroSlide);
}
if (!$heroManaged && $heroSlides === []) {
$heroSlides = [[
    'heading' => $pageTitle,
    'subheading' => (string) ($service['summary'] ?? ''),
    'description' => '',
    'media_type' => 'image',
    'background_media' => (string) ($service['image'] ?? ''),
    'primary_cta_label' => 'Discuss your requirement',
    'primary_cta_url' => $contactHref,
    'secondary_cta_label' => 'Explore capabilities',
    'secondary_cta_url' => '#' . $firstContentSection,
]];
}
?>

<article
    class="service-page"
    style="--service-accent: <?php echo $this->escape($theme['accent']); ?>; --service-accent-2: <?php echo $this->escape($theme['accent2']); ?>;"
>
    <?php if ($heroSlides !== []) : ?>
    <section class="service-hero relative isolate overflow-hidden bg-desnky-dark text-white" x-data="{ activeHero: 0 }">
        <?php foreach ($heroSlides as $slideIndex => $slide) : ?>
            <div x-show="activeHero === <?php echo $slideIndex; ?>" x-transition.opacity class="absolute inset-0 -z-20">
                <?php if (!empty($slide['background_media']) && ($slide['media_type'] ?? 'image') === 'video') : ?>
                    <video src="<?php echo $this->escape((string) $slide['background_media']); ?>" class="h-full w-full object-cover" autoplay muted loop playsinline preload="metadata"></video>
                <?php elseif (!empty($slide['background_media'])) : ?>
                    <img src="<?php echo $this->escape((string) $slide['background_media']); ?>" alt="" class="h-full w-full object-cover" <?php echo $slideIndex === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"'; ?>>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <div class="service-hero__overlay absolute inset-0 -z-10"></div>
        <div class="container-page py-16 sm:py-20 lg:py-28">
            <nav class="text-sm text-white/75" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="/" class="hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Home</a></li>
                    <li aria-hidden="true">/</li>
                    <li><a href="/services" class="hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Services</a></li>
                    <li aria-hidden="true">/</li>
                    <li><span class="text-white" aria-current="page"><?php echo $this->escape($pageTitle); ?></span></li>
                </ol>
            </nav>

            <?php foreach ($heroSlides as $slideIndex => $slide) : ?>
            <div x-show="activeHero === <?php echo $slideIndex; ?>" x-transition class="mt-10 grid gap-10 lg:grid-cols-[minmax(0,1fr)_25rem] lg:items-end">
                <div class="max-w-4xl">
                    <span class="service-hero__mark" aria-hidden="true">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => $icon, 'class' => 'h-8 w-8']); ?>
                    </span>
                    <p class="mt-6 text-sm font-bold uppercase tracking-wide text-white/75"><?php echo $this->escape((string) ($service['category'] ?? 'Service')); ?></p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                        <?php echo $this->escape((string) $slide['heading']); ?>
                    </h1>
                    <?php if (!empty($slide['subheading'])) : ?>
                        <p class="mt-5 max-w-3xl text-2xl font-semibold leading-snug text-white sm:text-3xl">
                            <?php echo $this->escape((string) $slide['subheading']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($slide['description'])) : ?>
                        <p class="mt-5 max-w-3xl text-base leading-8 text-white/85 sm:text-lg">
                            <?php echo $this->escape((string) $slide['description']); ?>
                        </p>
                    <?php endif; ?>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <?php if (!empty($slide['primary_cta_label']) && !empty($slide['primary_cta_url'])) : ?><a href="<?php echo $this->escape((string) $slide['primary_cta_url']); ?>" class="btn-on-dark" data-analytics-event="service_cta"><?php echo $this->escape((string) $slide['primary_cta_label']); ?></a><?php endif; ?>
                        <?php if (!empty($slide['secondary_cta_label']) && !empty($slide['secondary_cta_url'])) : ?><a href="<?php echo $this->escape((string) $slide['secondary_cta_url']); ?>" class="btn-secondary-on-dark"><?php echo $this->escape((string) $slide['secondary_cta_label']); ?></a><?php endif; ?>
                    </div>
                    <?php if (count($heroSlides) > 1) : ?>
                        <div class="mt-8 flex items-center gap-2" aria-label="Choose hero slide">
                            <?php foreach ($heroSlides as $dotIndex => $_slide) : ?><button type="button" @click="activeHero = <?php echo $dotIndex; ?>" class="h-2.5 rounded-full bg-white transition-all" :class="activeHero === <?php echo $dotIndex; ?> ? 'w-8' : 'w-2.5 opacity-50'" aria-label="Show slide <?php echo $dotIndex + 1; ?>"></button><?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($capabilityPills !== []) : ?>
                    <aside class="service-hero__panel" aria-label="Core service areas">
                        <p class="text-sm font-bold uppercase tracking-wide text-white/70">Core focus</p>
                        <div class="mt-4 grid gap-3">
                            <?php foreach ($capabilityPills as $pill) : ?>
                                <a href="#<?php echo $this->escape((string) ($capabilitySection['id'] ?? 'service-content')); ?>" class="service-hero__pill">
                                    <span class="service-hero__pill-dot" aria-hidden="true"></span>
                                    <span><?php echo $this->escape($pill); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (count($navItems) > 1) : ?>
        <div class="service-subnav sticky top-20 z-30 hidden border-b border-gray-200 bg-white/95 backdrop-blur lg:block">
            <div class="container-page">
                <nav class="flex gap-2 overflow-x-auto py-3" aria-label="On this page">
                    <?php foreach ($navItems as $item) : ?>
                        <a href="#<?php echo $this->escape((string) $item['id']); ?>" class="service-subnav__link">
                            <?php echo $this->escape((string) $item['label']); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
    <?php endif; ?>

    <section id="overview" class="section-band bg-white scroll-mt-28">
        <div class="container-page grid gap-10 lg:grid-cols-[minmax(0,1fr)_28rem] lg:items-start">
            <div class="max-w-4xl" data-reveal>
                <p class="eyebrow">Overview</p>
                <h2 class="mt-3 section-heading"><?php echo $this->escape($introHeading !== '' ? $introHeading : $pageTitle); ?></h2>
                <div class="mt-6">
                    <?php $renderBlocks($introBlocks); ?>
                </div>
            </div>

            <div class="space-y-5" data-reveal>
                <div class="service-visual-panel">
                    <?php if ($heroImage !== '') : ?>
                        <img
                            src="<?php echo $this->escape($heroImage); ?>"
                            alt="<?php echo $this->escape($pageTitle . ' supporting visual'); ?>"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        >
                    <?php else : ?>
                        <div class="flex h-full min-h-80 items-center justify-center bg-desnky-surface text-desnky-primary">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => $icon, 'class' => 'h-14 w-14']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($legacyFeatures !== []) : ?>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-bold uppercase tracking-wide text-desnky-primary">Quick highlights</p>
                        <ul class="mt-4 grid gap-2 text-sm leading-6 text-desnky-ink">
                            <?php foreach ($legacyFeatures as $feature) : ?>
                                <li class="service-list-item">
                                    <span class="service-list-item__icon" aria-hidden="true">
                                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-3.5 w-3.5']); ?>
                                    </span>
                                    <span><?php echo $this->escape((string) $feature); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if ($slug === 'engineering') : ?>
        <?php echo $this->partial('frontend/pages/services/partials/engineering-details'); ?>
    <?php elseif (in_array($slug, ['energy-solutions', 'procurement', 'ict-solutions', 'agro-food-processing', 'hse-safety'], true)) : ?>
        <?php echo $this->partial('frontend/pages/services/partials/modern-service-details', ['slug' => $slug]); ?>
    <?php else : ?>
    <?php foreach ($mainSections as $index => $section) : ?>
        <?php
        $title = (string) ($section['title'] ?? '');
        $id = (string) ($section['id'] ?? '');
        $blocks = \is_array($section['blocks'] ?? null) ? $section['blocks'] : [];
        $children = \is_array($section['children'] ?? null) ? $section['children'] : [];
        $isProcess = $sectionMatches($section, '/process|approach/i') && $children !== [];
        $isBenefits = $sectionMatches($section, '/^why\s|why choose|why work/i') && $children !== [];
        $isMarket = $sectionMatches($section, '/industries|sectors|markets|who we supply/i') && $children !== [];
        $isAssurance = $sectionMatches($section, '/safety|quality|compliance|hygiene|responsible/i');
        $bandClass = $index % 2 === 0 ? 'bg-desnky-surface' : 'bg-white';
        ?>

        <section id="<?php echo $this->escape($id); ?>" class="section-band <?php echo $bandClass; ?> scroll-mt-28">
            <div class="container-page">
                <div class="max-w-4xl" data-reveal>
                    <p class="eyebrow"><?php echo $isProcess ? 'Our process' : ($isBenefits ? 'Value' : ($isMarket ? 'Markets' : 'Service Detail')); ?></p>
                    <h2 class="mt-3 section-heading"><?php echo $this->escape($title); ?></h2>
                    <?php if ($blocks !== []) : ?>
                        <div class="mt-5 max-w-3xl">
                            <?php $renderBlocks($blocks); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($isProcess) : ?>
                    <ol class="service-timeline mt-10">
                        <?php foreach ($children as $stepIndex => $child) : ?>
                            <?php if (!\is_array($child)) { continue; } ?>
                            <li class="service-timeline__item" data-reveal>
                                <div class="service-timeline__number"><?php echo str_pad((string) ($stepIndex + 1), 2, '0', STR_PAD_LEFT); ?></div>
                                <div class="min-w-0">
                                    <h3 class="text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) ($child['title'] ?? '')); ?></h3>
                                    <?php $renderBlocks(\is_array($child['blocks'] ?? null) ? $child['blocks'] : [], 'compact'); ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php elseif ($children !== []) : ?>
                    <div class="<?php echo $isBenefits || $isMarket ? 'service-compact-grid' : 'service-detail-grid'; ?> mt-10">
                        <?php foreach ($children as $childIndex => $child) : ?>
                            <?php if (!\is_array($child)) { continue; } ?>
                            <article class="<?php echo $isBenefits || $isMarket ? 'service-info-card' : 'service-detail-card'; ?>" data-reveal>
                                <div class="service-card-count" aria-hidden="true"><?php echo str_pad((string) ($childIndex + 1), 2, '0', STR_PAD_LEFT); ?></div>
                                <h3 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($child['title'] ?? '')); ?></h3>
                                <?php $renderBlocks(\is_array($child['blocks'] ?? null) ? $child['blocks'] : [], $isBenefits || $isMarket ? 'compact' : 'default'); ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($isAssurance) : ?>
                    <div class="mt-10 rounded-lg border border-gray-200 bg-white p-6 shadow-card">
                        <?php $renderBlocks($blocks); ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($featuredProjects !== []) : ?>
        <section id="projects" class="section-band bg-white scroll-mt-28">
            <div class="container-page">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal>
                    <div>
                        <p class="eyebrow">Our work</p>
                        <h2 class="mt-3 section-heading">Related projects</h2>
                    </div>
                    <a href="/projects" class="btn-secondary shrink-0">View all projects</a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <?php foreach ($featuredProjects as $project) : ?>
                        <article class="card-interactive flex flex-col" data-reveal>
                            <?php if (!empty($project['image'])) : ?>
                                <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="block overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-desnky-primary">
                                    <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape((string) $project['title']); ?>" class="h-52 w-full object-cover transition-transform duration-slow hover:scale-105" loading="lazy">
                                </a>
                            <?php endif; ?>
                            <div class="card-body flex flex-1 flex-col">
                                <?php if (!empty($project['category'])) : ?>
                                    <span class="badge-brand mb-3 self-start"><?php echo $this->escape((string) $project['category']); ?></span>
                                <?php endif; ?>
                                <h3 class="text-lg font-bold text-desnky-dark">
                                    <a href="/projects/<?php echo $this->escape((string) $project['slug']); ?>" class="hover:text-desnky-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-desnky-primary"><?php echo $this->escape((string) $project['title']); ?></a>
                                </h3>
                                <p class="mt-2 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $project['summary']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($trustedClients !== []) : ?>
        <section id="clients" class="section-band engineering-clients scroll-mt-28">
            <div class="container-page">
                <div class="mx-auto max-w-3xl text-center" data-reveal>
                    <p class="eyebrow">Client confidence</p>
                    <h2 class="mt-3 section-heading">Trusted by leading organisations</h2>
                    <p class="mt-4 text-base leading-7 text-desnky-muted">Supporting organisations with dependable engineering, maintenance and project delivery.</p>
                </div>
                <?php
                $clientItems = array_values($trustedClients);
                $clientRows = [[], []];
                foreach ($clientItems as $clientIndex => $client) {
                    $clientRows[$clientIndex % 2][] = $client;
                }
                if ($clientRows[1] === []) {
                    $clientRows[1] = $clientRows[0];
                }
                $renderServiceClient = function (array $client, bool $duplicate = false): void {
                    $name = (string) ($client['name'] ?? '');
                    $logo = (string) ($client['logo'] ?? '');
                    $url = (string) ($client['website_url'] ?? '');
                    $hidden = $duplicate ? ' aria-hidden="true"' : '';
                    $hiddenLink = $duplicate ? ' aria-hidden="true" tabindex="-1"' : '';
                    ?>
                    <?php if ($url !== '') : ?>
                        <a href="<?php echo $this->escape($url); ?>" target="_blank" rel="noopener noreferrer" class="trusted-client-card"<?php echo $hiddenLink; ?>>
                    <?php else : ?>
                        <div class="trusted-client-card"<?php echo $hidden; ?>>
                    <?php endif; ?>
                        <?php if ($logo !== '') : ?>
                            <img src="<?php echo $this->escape($logo); ?>" alt="<?php echo $this->escape($name); ?>" class="trusted-client-logo" loading="lazy">
                        <?php else : ?>
                            <span class="trusted-client-name"><?php echo $this->escape($name); ?></span>
                        <?php endif; ?>
                    <?php if ($url !== '') : ?></a><?php else : ?></div><?php endif; ?>
                    <?php
                };
                ?>
                <div class="trusted-carousel mt-10" aria-label="Trusted clients carousel" data-reveal>
                    <div class="trusted-carousel__mobile">
                        <div class="trusted-carousel__track">
                            <?php foreach ($clientItems as $client) { $renderServiceClient($client); } ?>
                            <?php foreach ($clientItems as $client) { $renderServiceClient($client, true); } ?>
                        </div>
                    </div>
                    <div class="trusted-carousel__desktop">
                        <?php foreach ($clientRows as $rowIndex => $row) : ?>
                            <div class="trusted-carousel__track <?php echo $rowIndex === 1 ? 'trusted-carousel__track--reverse' : ''; ?>">
                                <?php foreach ($row as $client) { $renderServiceClient($client); } ?>
                                <?php foreach ($row as $client) { $renderServiceClient($client, true); } ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($faqs !== []) : ?>
        <section id="faqs" class="section-band bg-white scroll-mt-28">
            <div class="container-page max-w-4xl">
                <div class="text-center" data-reveal>
                    <p class="eyebrow">FAQs</p>
                    <h2 class="mt-3 section-heading">Frequently asked questions</h2>
                </div>
                <div class="mt-10 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white shadow-card" x-data="{ open: 0 }">
                    <?php foreach ($faqs as $i => $faq) : ?>
                        <?php if (!\is_array($faq)) { continue; } ?>
                        <div>
                            <h3>
                                <button
                                    type="button"
                                    id="service-faq-button-<?php echo $i; ?>"
                                    class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-desnky-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-desnky-primary"
                                    @click="open = open === <?php echo $i; ?> ? null : <?php echo $i; ?>"
                                    :aria-expanded="(open === <?php echo $i; ?>).toString()"
                                    aria-controls="service-faq-panel-<?php echo $i; ?>"
                                >
                                    <?php echo $this->escape((string) ($faq['question'] ?? '')); ?>
                                    <span :class="open === <?php echo $i; ?> && 'rotate-180'" class="shrink-0 text-desnky-primary transition-transform">
                                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-5 w-5']); ?>
                                    </span>
                                </button>
                            </h3>
                            <div id="service-faq-panel-<?php echo $i; ?>" x-show="open === <?php echo $i; ?>" x-collapse x-cloak role="region" aria-labelledby="service-faq-button-<?php echo $i; ?>">
                                <p class="px-6 pb-5 text-sm leading-7 text-desnky-muted"><?php echo $this->escape((string) ($faq['answer'] ?? '')); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($finalCtaSection !== null) : ?>
        <section class="service-cta-panel bg-desnky-dark py-16 text-white">
            <div class="container-page grid gap-8 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                <div class="max-w-3xl" data-reveal>
                    <p class="eyebrow-on-dark">Next step</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight sm:text-4xl"><?php echo $this->escape((string) ($finalCtaSection['title'] ?? 'Ready to work with us?')); ?></h2>
                    <div class="mt-4 text-white/80">
                        <?php $renderBlocks(\is_array($finalCtaSection['blocks'] ?? null) ? $finalCtaSection['blocks'] : [], 'compact'); ?>
                    </div>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row lg:flex-col" data-reveal>
                    <a href="<?php echo $this->escape($contactHref); ?>" class="btn-on-dark">
                        <?php echo $this->escape((string) ($finalPrimaryCta['label'] ?? $primaryCta['label'] ?? 'Request a quote')); ?>
                    </a>
                    <a href="<?php echo $this->escape($contactHref); ?>" class="btn-secondary-on-dark">
                        <?php echo $this->escape((string) ($finalSecondaryCta['label'] ?? 'Speak with our team')); ?>
                    </a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($contactSection !== null) : ?>
        <section id="enquiry" class="section-band bg-desnky-surface scroll-mt-28">
            <div class="container-page grid gap-8 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-start">
                <div class="max-w-4xl" data-reveal>
                    <p class="eyebrow">Enquiry</p>
                    <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($contactSection['title'] ?? 'Contact our team')); ?></h2>
                    <div class="mt-5">
                        <?php $renderBlocks(\is_array($contactSection['blocks'] ?? null) ? $contactSection['blocks'] : []); ?>
                    </div>
                </div>
                <aside class="rounded-lg bg-white p-6 shadow-card ring-1 ring-gray-200" data-reveal>
                    <p class="text-sm font-bold uppercase tracking-wide text-desnky-primary">Start the conversation</p>
                    <p class="mt-3 text-sm leading-6 text-desnky-muted">Use the contact form so the right team can review your requirement and respond with the appropriate next steps.</p>
                    <a href="<?php echo $this->escape($contactHref); ?>" class="btn-primary mt-6 w-full">
                        <?php echo $this->escape((string) (($documentation['form_button'] ?? '') ?: $primaryCta['label'] ?? 'Submit enquiry')); ?>
                    </a>
                    <a href="/services" class="btn-secondary mt-3 w-full">View all services</a>
                </aside>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($relatedServices !== []) : ?>
        <section class="section-band bg-white">
            <div class="container-page">
                <h2 class="section-heading" data-reveal><?php echo $this->escape((string) ($detail['related_heading'] ?? 'Related services')); ?></h2>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    <?php foreach (array_slice($relatedServices, 0, 3) as $related) : ?>
                        <a href="/services/<?php echo $this->escape((string) $related['slug']); ?>" class="card-interactive block focus:outline-none focus-visible:ring-2 focus-visible:ring-desnky-primary" data-reveal>
                            <div class="card-body">
                                <span class="icon-tile">
                                    <?php echo $this->partial('frontend/partials/icon', ['name' => $iconFor((string) $related['slug']), 'class' => 'h-6 w-6']); ?>
                                </span>
                                <h3 class="mt-5 text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) $related['title']); ?></h3>
                                <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $related['summary']); ?></p>
                                <span class="btn-ghost mt-4">
                                    Explore <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>
