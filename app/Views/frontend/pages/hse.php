<?php
$hero = $hero ?? [];
$policyStatement = $policyStatement ?? [];
$objective = $objective ?? [];
$managementCommitments = $managementCommitments ?? [];
$communication = $communication ?? [];
$culture = $culture ?? [];
$cta = $cta ?? [];
$commitmentItems = is_array($managementCommitments['items'] ?? null) ? $managementCommitments['items'] : [];
?>

<?php echo $this->partial('frontend/partials/page-hero', [
    'hero' => $hero,
    'title' => $title ?? '',
    'fallbackImage' => $page['featured_image'] ?? '',
]); ?>

<?php if ($policyStatement !== []) : ?>
<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div>
            <?php if (!empty($policyStatement['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $policyStatement['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($policyStatement['heading'] ?? '')); ?></h2>
            <div class="mt-6 space-y-5 text-base leading-8 text-desnky-muted">
                <?php foreach ((array) ($policyStatement['paragraphs'] ?? []) as $paragraph) : ?>
                    <p><?php echo $this->escape((string) $paragraph); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if (!empty($policyStatement['image'])) : ?>
            <img
                src="<?php echo $this->escape((string) $policyStatement['image']); ?>"
                alt="<?php echo $this->escape((string) ($policyStatement['image_alt'] ?? $policyStatement['heading'] ?? '')); ?>"
                class="h-80 w-full object-cover shadow-card"
                loading="lazy"
            >
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($objective !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <?php if (!empty($objective['image'])) : ?>
            <img
                src="<?php echo $this->escape((string) $objective['image']); ?>"
                alt="<?php echo $this->escape((string) ($objective['image_alt'] ?? $objective['heading'] ?? '')); ?>"
                class="h-72 w-full object-cover shadow-card"
                loading="lazy"
            >
        <?php endif; ?>
        <div>
            <?php if (!empty($objective['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-green"><?php echo $this->escape((string) $objective['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($objective['heading'] ?? '')); ?></h2>
            <?php if (!empty($objective['text'])) : ?>
                <p class="mt-5 text-lg leading-8 text-desnky-muted"><?php echo $this->escape((string) $objective['text']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($commitmentItems !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-end">
            <div class="max-w-3xl">
                <?php if (!empty($managementCommitments['eyebrow'])) : ?>
                    <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $managementCommitments['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($managementCommitments['heading'] ?? '')); ?></h2>
                <?php if (!empty($managementCommitments['text'])) : ?>
                    <p class="mt-4 leading-7 text-desnky-muted"><?php echo $this->escape((string) $managementCommitments['text']); ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($managementCommitments['image'])) : ?>
                <img
                    src="<?php echo $this->escape((string) $managementCommitments['image']); ?>"
                    alt="<?php echo $this->escape((string) ($managementCommitments['image_alt'] ?? $managementCommitments['heading'] ?? '')); ?>"
                    class="h-64 w-full object-cover shadow-card"
                    loading="lazy"
                >
            <?php endif; ?>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-2">
            <?php foreach ($commitmentItems as $index => $item) : ?>
                <?php if (!is_array($item)) continue; ?>
                <article class="rounded-lg border border-gray-200 bg-white p-6 shadow-card">
                    <span class="text-sm font-bold text-desnky-green"><?php echo $this->escape(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                    <h3 class="mt-4 text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($item['title'] ?? '')); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) ($item['text'] ?? '')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($communication !== [] || $culture !== []) : ?>
<section class="section-band bg-desnky-navy text-white">
    <div class="container-page grid gap-8 lg:grid-cols-2">
        <?php foreach ([$communication, $culture] as $section) : ?>
            <?php if (!is_array($section) || $section === []) continue; ?>
            <article class="overflow-hidden rounded-lg border border-white/15 bg-white/5">
                <?php if (!empty($section['image'])) : ?>
                    <img
                        src="<?php echo $this->escape((string) $section['image']); ?>"
                        alt="<?php echo $this->escape((string) ($section['image_alt'] ?? $section['heading'] ?? '')); ?>"
                        class="h-56 w-full object-cover opacity-90"
                        loading="lazy"
                    >
                <?php endif; ?>
                <div class="p-7">
                <?php if (!empty($section['eyebrow'])) : ?>
                    <p class="text-sm font-semibold uppercase tracking-wide text-desnky-gold"><?php echo $this->escape((string) $section['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 text-2xl font-bold"><?php echo $this->escape((string) ($section['heading'] ?? '')); ?></h2>
                <?php if (!empty($section['text'])) : ?>
                    <p class="mt-4 leading-7 text-gray-200"><?php echo $this->escape((string) $section['text']); ?></p>
                <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($cta !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page flex flex-col justify-between gap-6 md:flex-row md:items-center">
        <div class="max-w-2xl">
            <h2 class="text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($cta['heading'] ?? '')); ?></h2>
            <?php if (!empty($cta['text'])) : ?>
                <p class="mt-4 leading-7 text-desnky-muted"><?php echo $this->escape((string) $cta['text']); ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($cta['primary_cta_label']) && !empty($cta['primary_cta_url'])) : ?>
            <a href="<?php echo $this->escape((string) $cta['primary_cta_url']); ?>" class="btn-primary">
                <?php echo $this->escape((string) $cta['primary_cta_label']); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
