<?php
$project = $project ?? [];
$detail = $detail ?? [];
$gallery = is_array($project['gallery'] ?? null) ? $project['gallery'] : [];
$highlights = is_array($project['highlights'] ?? null) ? $project['highlights'] : [];
$outcomes = is_array($project['outcomes'] ?? null) ? $project['outcomes'] : [];
?>

<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <?php if (!empty($project['image'])) : ?>
        <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape((string) $project['title']); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-30" loading="eager" fetchpriority="high">
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-desnky-dark/85 via-desnky-dark/80 to-desnky-dark"></div>
    <div class="container-page py-16 sm:py-20 lg:py-24">
        <nav class="text-sm text-gray-300" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? 'Home')); ?></a></li>
                <li class="text-gray-500" aria-hidden="true">/</li>
                <li><a href="/projects" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_projects_label'] ?? 'Projects')); ?></a></li>
                <li class="text-gray-500" aria-hidden="true">/</li>
                <li><span class="text-desnky-primary-200" aria-current="page"><?php echo $this->escape((string) $project['title']); ?></span></li>
            </ol>
        </nav>
        <div class="mt-8 max-w-3xl">
            <?php if (!empty($project['category'])) : ?>
                <span class="badge-brand bg-white/10 text-desnky-primary-200"><?php echo $this->escape((string) $project['category']); ?></span>
            <?php endif; ?>
            <h1 class="mt-4 text-4xl font-bold leading-tight sm:text-5xl"><?php echo $this->escape((string) $project['title']); ?></h1>
            <p class="mt-5 text-lg leading-8 text-gray-200"><?php echo $this->escape((string) $project['summary']); ?></p>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-12 lg:grid-cols-[1fr_22rem]">
        <div>
            <h2 class="section-heading"><?php echo $this->escape((string) ($detail['overview_heading'] ?? 'Project overview')); ?></h2>
            <?php if (!empty($project['overview'])) : ?>
                <div class="prose-desnky mt-6"><?php echo nl2br($this->escape((string) $project['overview'])); ?></div>
            <?php endif; ?>

            <?php if ($gallery !== []) : ?>
                <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <?php foreach ($gallery as $index => $image) : ?>
                        <?php $path = (string) ($image['path'] ?? ''); ?>
                        <?php if ($path === '') {
                            continue;
                        } ?>
                        <button type="button" class="overflow-hidden rounded-lg" data-lightbox="<?php echo $this->escape($path); ?>" data-lightbox-alt="<?php echo $this->escape((string) ($image['alt_text'] ?? $project['title'])); ?>" aria-label="View project image <?php echo (int) $index + 1; ?>">
                            <img src="<?php echo $this->escape($path); ?>" alt="<?php echo $this->escape((string) ($image['alt_text'] ?: $project['title'])); ?>" class="aspect-square w-full object-cover transition-transform duration-slow hover:scale-105" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($highlights !== []) : ?>
                <h2 class="mt-14 section-heading"><?php echo $this->escape((string) ($detail['highlights_heading'] ?? 'Scope & approach')); ?></h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($highlights as $highlight) : ?>
                        <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-card" data-reveal>
                            <p class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $highlight); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($outcomes !== []) : ?>
                <h2 class="mt-14 section-heading"><?php echo $this->escape((string) ($detail['outcomes_heading'] ?? 'Outcomes')); ?></h2>
                <ul class="mt-6 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($outcomes as $outcome) : ?>
                        <li class="flex items-start gap-3 rounded-lg bg-desnky-secondary-50 p-5 text-sm font-semibold text-desnky-dark">
                            <span class="mt-0.5 text-desnky-secondary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'check-circle', 'class' => 'h-5 w-5']); ?></span>
                            <?php echo $this->escape((string) $outcome); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <aside class="lg:sticky lg:top-28 lg:self-start">
            <div class="rounded-lg border border-gray-200 bg-desnky-surface p-6 shadow-card">
                <h2 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($detail['summary_heading'] ?? 'Project details')); ?></h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <?php if (!empty($project['category'])) : ?>
                        <div><dt class="font-semibold text-desnky-primary"><?php echo $this->escape((string) ($detail['category_label'] ?? 'Sector')); ?></dt><dd class="mt-1 text-desnky-muted"><?php echo $this->escape((string) $project['category']); ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($project['client_name'])) : ?>
                        <div><dt class="font-semibold text-desnky-primary"><?php echo $this->escape((string) ($detail['client_label'] ?? 'Client')); ?></dt><dd class="mt-1 text-desnky-muted"><?php echo $this->escape((string) $project['client_name']); ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($project['project_date'])) : ?>
                        <div><dt class="font-semibold text-desnky-primary"><?php echo $this->escape((string) ($detail['date_label'] ?? 'Completed')); ?></dt><dd class="mt-1 text-desnky-muted"><?php echo $this->escape($this->formatDate((string) $project['project_date'])); ?></dd></div>
                    <?php endif; ?>
                </dl>
                <div class="mt-6 rounded-md bg-desnky-dark p-5 text-white">
                    <p class="font-semibold">Start your project</p>
                    <p class="mt-1 text-sm text-gray-300">Let's deliver similar results for you.</p>
                    <a href="<?php echo $this->escape((string) ($detail['cta_url'] ?? '/contact')); ?>" class="btn-on-dark mt-4 w-full"><?php echo $this->escape((string) ($detail['cta_label'] ?? 'Request a Quote')); ?></a>
                </div>
            </div>
        </aside>
    </div>
</section>
