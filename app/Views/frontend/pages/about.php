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
$services = $services ?? [];
$sectorItems = is_array($sectors['items'] ?? null) ? $sectors['items'] : [];
$modelItems = is_array($operatingModel['steps'] ?? null) ? $operatingModel['steps'] : [];
$valueItems = is_array($values['items'] ?? null) ? $values['items'] : [];
$statItems = is_array($stats['items'] ?? null) ? $stats['items'] : [];
$teamMembers = is_array($team['members'] ?? null) ? $team['members'] : [];
$profileImage = (string) ($overview['image'] ?? $hero['image'] ?? $page['featured_image'] ?? '');
$profileImageAlt = (string) ($overview['image_alt'] ?? $hero['image_alt'] ?? 'Desnky Global Resources operations team');
?>

<?php if ($overview !== []) : ?>
<section class="section-band bg-white">
    <div class="container-page grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div class="profile-image-panel" data-reveal>
            <?php if ($profileImage !== '') : ?>
                <img
                    src="<?php echo $this->escape($profileImage); ?>"
                    alt="<?php echo $this->escape($profileImageAlt); ?>"
                    class="h-full w-full object-cover"
                    loading="eager"
                    decoding="async"
                >
            <?php else : ?>
                <div class="flex aspect-[4/5] w-full items-center justify-center bg-desnky-surface text-sm font-semibold text-desnky-muted">Desnky Global Resources Ltd</div>
            <?php endif; ?>
        </div>
        <div data-reveal>
            <?php if (!empty($overview['eyebrow'])) : ?>
                <p class="eyebrow"><?php echo $this->escape((string) $overview['eyebrow']); ?></p>
            <?php endif; ?>
            <h1 class="mt-3 section-heading"><?php echo $this->escape((string) ($overview['heading'] ?? $title ?? 'About Desnky Global Resources')); ?></h1>
            <?php if (!empty($hero['heading'])) : ?>
                <p class="mt-4 text-xl font-semibold leading-8 text-desnky-dark"><?php echo $this->escape((string) $hero['heading']); ?></p>
            <?php endif; ?>
            <div class="mt-6 space-y-5 text-base leading-8 text-desnky-muted">
                <?php foreach ((array) ($overview['paragraphs'] ?? []) as $paragraph) : ?>
                    <p><?php echo $this->escape((string) $paragraph); ?></p>
                <?php endforeach; ?>
            </div>
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

<?php if ($services !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="max-w-3xl">
            <?php if (!empty($sectors['eyebrow'])) : ?>
                <p class="eyebrow"><?php echo $this->escape((string) $sectors['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($sectors['heading'] ?? 'The sectors we support')); ?></h2>
            <?php if (!empty($sectors['text'])) : ?>
                <p class="section-lead"><?php echo $this->escape((string) $sectors['text']); ?></p>
            <?php endif; ?>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <?php if (!is_array($service)) continue; ?>
                <article class="card-interactive service-card flex flex-col" data-reveal>
                    <?php if (!empty($service['image'])) : ?>
                        <img
                            src="<?php echo $this->escape((string) $service['image']); ?>"
                            alt="<?php echo $this->escape((string) ($service['title'] ?? 'Service image')); ?>"
                            class="service-card__image"
                            loading="lazy"
                        >
                    <?php endif; ?>
                    <div class="card-body flex flex-1 flex-col">
                        <h3 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($service['title'] ?? '')); ?></h3>
                        <p class="mt-3 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) ($service['summary'] ?? '')); ?></p>
                        <?php if (!empty($service['slug'])) : ?>
                            <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="btn-ghost mt-5">
                                Learn more
                                <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
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
                    <p class="eyebrow"><?php echo $this->escape((string) $team['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($team['heading'] ?? 'Our Team')); ?></h2>
                <?php if (!empty($team['text'])) : ?>
                    <p class="section-lead"><?php echo $this->escape((string) $team['text']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <?php foreach ($teamMembers as $member) : ?>
                <?php if (!is_array($member)) continue; ?>
                <article class="team-card" data-reveal>
                    <div class="team-card__media">
                        <?php if (!empty($member['image'])) : ?>
                            <img
                                src="<?php echo $this->escape((string) $member['image']); ?>"
                                alt="<?php echo $this->escape((string) ($member['image_alt'] ?? $member['name'] ?? 'Team member')); ?>"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        <?php else : ?>
                            <div class="flex h-full w-full items-center justify-center bg-desnky-primary-50 text-2xl font-bold text-desnky-primary">
                                <?php echo $this->escape(substr((string) ($member['name'] ?? 'D'), 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) ($member['name'] ?? '')); ?></h3>
                        <p class="mt-2 text-sm font-semibold text-desnky-primary"><?php echo $this->escape((string) ($member['role'] ?? '')); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
