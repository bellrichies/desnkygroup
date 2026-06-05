<?php
$hero = $hero ?? [];
$overview = $overview ?? [];
$missionVision = $missionVision ?? [];
$sectors = $sectors ?? [];
$operatingModel = $operatingModel ?? [];
$values = $values ?? [];
$stats = $stats ?? [];
$team = $team ?? [];
$cta = $cta ?? [];
$sectorItems = is_array($sectors['items'] ?? null) ? $sectors['items'] : [];
$modelItems = is_array($operatingModel['steps'] ?? null) ? $operatingModel['steps'] : [];
$valueItems = is_array($values['items'] ?? null) ? $values['items'] : [];
$statItems = is_array($stats['items'] ?? null) ? $stats['items'] : [];
$teamMembers = is_array($team['members'] ?? null) ? $team['members'] : [];
?>

<?php echo $this->partial('frontend/partials/page-hero', [
    'hero' => $hero,
    'title' => $title ?? '',
    'fallbackImage' => $page['featured_image'] ?? '',
]); ?>

<?php if ($overview !== []) : ?>
<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[0.95fr_1.05fr] lg:items-start">
        <div>
            <?php if (!empty($overview['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $overview['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($overview['heading'] ?? '')); ?></h2>
        </div>
        <div class="space-y-5 text-base leading-8 text-desnky-muted">
            <?php foreach ((array) ($overview['paragraphs'] ?? []) as $paragraph) : ?>
                <p><?php echo $this->escape((string) $paragraph); ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($missionVision !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page grid gap-6 md:grid-cols-2">
        <?php foreach (['mission', 'vision'] as $key) : ?>
            <?php $item = is_array($missionVision[$key] ?? null) ? $missionVision[$key] : []; ?>
            <?php if ($item !== []) : ?>
                <article class="rounded-lg border border-gray-200 bg-white p-7 shadow-card">
                    <p class="text-sm font-semibold uppercase tracking-wide text-desnky-green"><?php echo $this->escape((string) ($item['label'] ?? '')); ?></p>
                    <h2 class="mt-3 text-2xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($item['heading'] ?? '')); ?></h2>
                    <?php if (!empty($item['text'])) : ?>
                        <p class="mt-4 leading-7 text-desnky-muted"><?php echo $this->escape((string) $item['text']); ?></p>
                    <?php endif; ?>
                </article>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($sectorItems !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="max-w-3xl">
            <?php if (!empty($sectors['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $sectors['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($sectors['heading'] ?? '')); ?></h2>
            <?php if (!empty($sectors['text'])) : ?>
                <p class="mt-4 leading-7 text-desnky-muted"><?php echo $this->escape((string) $sectors['text']); ?></p>
            <?php endif; ?>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($sectorItems as $item) : ?>
                <?php if (!is_array($item)) continue; ?>
                <article class="rounded-lg border border-gray-200 bg-white p-6 shadow-card">
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-desnky-surface text-sm font-bold text-desnky-blue">
                        <?php echo $this->escape((string) ($item['code'] ?? '')); ?>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($item['title'] ?? '')); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) ($item['text'] ?? '')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($modelItems !== []) : ?>
<section class="section-band bg-desnky-navy text-white">
    <div class="container-page">
        <div class="max-w-3xl">
            <?php if (!empty($operatingModel['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-gold"><?php echo $this->escape((string) $operatingModel['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold"><?php echo $this->escape((string) ($operatingModel['heading'] ?? '')); ?></h2>
            <?php if (!empty($operatingModel['text'])) : ?>
                <p class="mt-4 leading-7 text-gray-200"><?php echo $this->escape((string) $operatingModel['text']); ?></p>
            <?php endif; ?>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-4">
            <?php foreach ($modelItems as $index => $step) : ?>
                <?php if (!is_array($step)) continue; ?>
                <article class="rounded-lg border border-white/15 bg-white/5 p-6">
                    <span class="text-sm font-bold text-desnky-gold"><?php echo $this->escape(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                    <h3 class="mt-4 text-lg font-bold"><?php echo $this->escape((string) ($step['title'] ?? '')); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-gray-200"><?php echo $this->escape((string) ($step['text'] ?? '')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($valueItems !== [] || $statItems !== []) : ?>
<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
        <?php if ($valueItems !== []) : ?>
            <div>
                <?php if (!empty($values['eyebrow'])) : ?>
                    <p class="text-sm font-semibold uppercase tracking-wide text-desnky-green"><?php echo $this->escape((string) $values['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($values['heading'] ?? '')); ?></h2>
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($valueItems as $item) : ?>
                        <?php if (!is_array($item)) continue; ?>
                        <div class="rounded-r-lg border-l-4 border-desnky-secondary bg-desnky-surface p-5">
                            <h3 class="font-bold text-desnky-navy"><?php echo $this->escape((string) ($item['title'] ?? '')); ?></h3>
                            <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) ($item['text'] ?? '')); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($statItems !== []) : ?>
            <aside class="rounded-lg bg-desnky-surface p-7">
                <h2 class="text-2xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($stats['heading'] ?? '')); ?></h2>
                <div class="mt-6 grid gap-4">
                    <?php foreach ($statItems as $item) : ?>
                        <?php if (!is_array($item)) continue; ?>
                        <div class="rounded-lg border border-gray-200 bg-white p-5">
                            <p class="text-3xl font-bold text-desnky-blue"><?php echo $this->escape((string) ($item['value'] ?? '')); ?></p>
                            <p class="mt-2 text-sm font-semibold text-desnky-navy"><?php echo $this->escape((string) ($item['label'] ?? '')); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($teamMembers !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
            <div class="max-w-3xl">
                <?php if (!empty($team['eyebrow'])) : ?>
                    <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $team['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($team['heading'] ?? '')); ?></h2>
                <?php if (!empty($team['text'])) : ?>
                    <p class="mt-4 leading-7 text-desnky-muted"><?php echo $this->escape((string) $team['text']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($teamMembers as $member) : ?>
                <?php if (!is_array($member)) continue; ?>
                <article class="overflow-hidden rounded-lg bg-white shadow-card">
                    <?php if (!empty($member['image'])) : ?>
                        <img
                            src="<?php echo $this->escape((string) $member['image']); ?>"
                            alt="<?php echo $this->escape((string) ($member['image_alt'] ?? $member['name'] ?? '')); ?>"
                            class="h-72 w-full object-cover"
                            loading="lazy"
                        >
                    <?php endif; ?>
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-desnky-navy"><?php echo $this->escape((string) ($member['name'] ?? '')); ?></h3>
                        <p class="mt-2 text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) ($member['role'] ?? '')); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
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
