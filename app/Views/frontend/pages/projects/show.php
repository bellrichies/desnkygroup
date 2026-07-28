<?php
$project = $project ?? [];
$detail = $detail ?? [];
$gallery = is_array($project['gallery'] ?? null) ? $project['gallery'] : [];
$highlights = is_array($project['highlights'] ?? null) ? $project['highlights'] : [];
$outcomes = is_array($project['outcomes'] ?? null) ? $project['outcomes'] : [];
$projectTitle = (string) ($project['title'] ?? 'Project');
$featuredImage = (string) ($project['image'] ?? '');
$galleryCount = count($gallery);
$overview = trim((string) ($project['overview'] ?? ''));
$overviewParagraphs = $overview === ''
    ? []
    : (preg_split('/\R{2,}/', $overview, -1, PREG_SPLIT_NO_EMPTY) ?: []);
?>

<section class="relative isolate overflow-hidden bg-desnky-dark text-white">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_85%_15%,rgba(108,52,131,0.42),transparent_34rem)]"></div>
    <div class="container-page pb-14 pt-8 sm:pb-16 lg:pb-20 lg:pt-10">
        <nav class="text-sm text-white/65" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? 'Home')); ?></a></li>
                <li aria-hidden="true"><?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5']); ?></li>
                <li><a href="/projects" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_projects_label'] ?? 'Projects')); ?></a></li>
                <li aria-hidden="true"><?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-right', 'class' => 'h-3.5 w-3.5']); ?></li>
                <li class="max-w-[14rem] truncate text-white sm:max-w-md" aria-current="page"><?php echo $this->escape($projectTitle); ?></li>
            </ol>
        </nav>

        <div class="mt-10 grid items-center gap-10 lg:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)] lg:gap-14">
            <div>
                <?php if (!empty($project['category'])) : ?>
                    <p class="eyebrow-on-dark"><?php echo $this->escape((string) $project['category']); ?></p>
                <?php endif; ?>
                <h1 class="mt-4 text-4xl font-extrabold leading-[1.08] tracking-tight sm:text-5xl lg:text-6xl"><?php echo $this->escape($projectTitle); ?></h1>
                <?php if (!empty($project['summary'])) : ?>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75"><?php echo $this->escape((string) $project['summary']); ?></p>
                <?php endif; ?>
                <a href="#project-overview" class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-white hover:text-desnky-primary-200">
                    Explore the case study
                    <span class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20" aria-hidden="true">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4']); ?>
                    </span>
                </a>
            </div>

            <?php if ($featuredImage !== '') : ?>
                <button type="button" class="group relative block w-full overflow-hidden rounded-2xl bg-white/5 text-left shadow-2xl ring-1 ring-white/10" data-lightbox="<?php echo $this->escape($featuredImage); ?>" data-lightbox-alt="<?php echo $this->escape($projectTitle); ?>" aria-label="View featured image for <?php echo $this->escape($projectTitle); ?>">
                    <img src="<?php echo $this->escape($featuredImage); ?>" alt="<?php echo $this->escape($projectTitle); ?>" class="aspect-[4/3] w-full object-cover transition duration-slow group-hover:scale-[1.02]" loading="eager" fetchpriority="high">
                    <span class="absolute bottom-4 right-4 inline-flex items-center gap-2 rounded-full bg-desnky-dark/80 px-4 py-2 text-xs font-semibold text-white backdrop-blur-md">
                        View image
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'plus', 'class' => 'h-4 w-4']); ?>
                    </span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="project-overview" class="section-band scroll-mt-24">
    <div class="container-page grid gap-12 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-20">
        <div>
            <p class="eyebrow">The project</p>
            <h2 class="mt-3 section-heading"><?php echo $this->escape((string) ($detail['overview_heading'] ?? 'Project overview')); ?></h2>
            <?php if ($overviewParagraphs !== []) : ?>
                <div class="mt-6 max-w-3xl border-l-4 border-desnky-primary pl-5 sm:pl-7">
                    <?php foreach ($overviewParagraphs as $paragraphIndex => $paragraph) : ?>
                        <p class="<?php echo $paragraphIndex === 0 ? 'text-xl font-medium leading-9 text-desnky-dark' : 'mt-5 text-base leading-7 text-desnky-muted'; ?>">
                            <?php echo nl2br($this->escape(trim((string) $paragraph))); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($highlights !== []) : ?>
                <div class="mt-12 border-t border-gray-200 pt-10">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-desnky-primary">Delivery scope</p>
                    <h2 class="mt-2 text-2xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($detail['highlights_heading'] ?? 'Scope & approach')); ?></h2>
                    <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                        <?php foreach ($highlights as $highlight) : ?>
                            <li class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 text-sm font-semibold leading-6 text-desnky-dark shadow-sm" data-reveal>
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-desnky-primary-50 text-desnky-primary" aria-hidden="true">
                                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-3.5 w-3.5']); ?>
                                </span>
                                <?php echo $this->escape((string) $highlight); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($outcomes !== []) : ?>
                <div class="mt-12 rounded-2xl bg-desnky-surface p-6 sm:p-8">
                    <p class="text-xs font-bold uppercase tracking-[0.16em] text-desnky-secondary">Project impact</p>
                    <h2 class="mt-2 text-2xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($detail['outcomes_heading'] ?? 'Outcomes')); ?></h2>
                    <ul class="mt-6 grid gap-4 sm:grid-cols-2">
                        <?php foreach ($outcomes as $outcome) : ?>
                            <li class="flex items-start gap-3 text-sm font-medium leading-6 text-desnky-dark">
                                <span class="mt-0.5 shrink-0 text-desnky-secondary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'check-circle', 'class' => 'h-5 w-5']); ?></span>
                                <?php echo $this->escape((string) $outcome); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <aside class="lg:sticky lg:top-28 lg:self-start">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-card">
                <div class="border-b border-gray-200 px-6 py-5">
                    <h2 class="text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) ($detail['summary_heading'] ?? 'Project details')); ?></h2>
                </div>
                <dl class="divide-y divide-gray-100 px-6 text-sm">
                    <?php if (!empty($project['category'])) : ?>
                        <div class="py-4"><dt class="text-xs font-semibold uppercase tracking-wider text-desnky-muted"><?php echo $this->escape((string) ($detail['category_label'] ?? 'Sector')); ?></dt><dd class="mt-1.5 font-semibold text-desnky-dark"><?php echo $this->escape((string) $project['category']); ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($project['client_name'])) : ?>
                        <div class="py-4"><dt class="text-xs font-semibold uppercase tracking-wider text-desnky-muted"><?php echo $this->escape((string) ($detail['client_label'] ?? 'Client')); ?></dt><dd class="mt-1.5 font-semibold text-desnky-dark"><?php echo $this->escape((string) $project['client_name']); ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($project['project_date'])) : ?>
                        <div class="py-4"><dt class="text-xs font-semibold uppercase tracking-wider text-desnky-muted"><?php echo $this->escape((string) ($detail['date_label'] ?? 'Completed')); ?></dt><dd class="mt-1.5 font-semibold text-desnky-dark"><?php echo $this->escape($this->formatDate((string) $project['project_date'])); ?></dd></div>
                    <?php endif; ?>
                </dl>
                <div class="m-3 rounded-xl bg-desnky-dark p-5 text-white">
                    <p class="font-bold">Have a similar project?</p>
                    <p class="mt-1 text-sm leading-6 text-white/65">Talk to our team about your requirements.</p>
                    <a href="<?php echo $this->escape((string) ($detail['cta_url'] ?? '/contact')); ?>" class="btn-on-dark mt-4 w-full">
                        <?php echo $this->escape((string) ($detail['cta_label'] ?? 'Request a Quote')); ?>
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                    </a>
                </div>
            </div>
        </aside>
    </div>
</section>

<?php if ($gallery !== []) : ?>
    <section class="bg-desnky-surface py-16 sm:py-20" aria-labelledby="project-gallery-heading">
        <div class="container-page">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end" data-reveal>
                <div>
                    <p class="eyebrow">Project gallery</p>
                    <h2 id="project-gallery-heading" class="mt-3 section-heading"><?php echo $this->escape((string) ($detail['gallery_heading'] ?? 'A closer look')); ?></h2>
                </div>
                <p class="text-sm text-desnky-muted"><?php echo $galleryCount; ?> <?php echo $galleryCount === 1 ? 'image' : 'images'; ?> · Select an image to enlarge</p>
            </div>

            <div class="mt-8 grid auto-rows-[12rem] grid-cols-2 gap-3 sm:auto-rows-[16rem] sm:gap-4 lg:grid-cols-4">
                <?php foreach ($gallery as $index => $image) : ?>
                    <?php
                    $path = (string) ($image['path'] ?? '');
                    if ($path === '') {
                        continue;
                    }
                    $alt = trim((string) ($image['alt_text'] ?? '')) ?: $projectTitle . ' gallery image ' . ((int) $index + 1);
                    $tileClass = $index === 0
                        ? 'col-span-2 row-span-2'
                        : (($index === 3 || $index === 6) ? 'col-span-2' : 'col-span-1');
                    ?>
                    <button type="button" class="group relative <?php echo $tileClass; ?> overflow-hidden rounded-xl bg-gray-200 text-left shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-desnky-primary focus-visible:ring-offset-2" data-lightbox="<?php echo $this->escape($path); ?>" data-lightbox-alt="<?php echo $this->escape($alt); ?>" aria-label="View image <?php echo (int) $index + 1; ?> of <?php echo $galleryCount; ?>: <?php echo $this->escape($alt); ?>">
                        <img src="<?php echo $this->escape($path); ?>" alt="<?php echo $this->escape($alt); ?>" class="h-full w-full object-cover transition duration-slow group-hover:scale-105" loading="lazy">
                        <span class="absolute inset-0 bg-gradient-to-t from-desnky-dark/40 via-transparent to-transparent opacity-0 transition-opacity duration-base group-hover:opacity-100"></span>
                        <span class="absolute bottom-3 right-3 flex h-9 w-9 translate-y-2 items-center justify-center rounded-full bg-white text-desnky-dark opacity-0 shadow-lg transition duration-base group-hover:translate-y-0 group-hover:opacity-100" aria-hidden="true">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'plus', 'class' => 'h-4 w-4']); ?>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="bg-white py-14 sm:py-16">
    <div class="container-page">
        <div class="flex flex-col items-start justify-between gap-6 border-t border-gray-200 pt-10 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-semibold text-desnky-primary">Discover more of our work</p>
                <p class="mt-1 text-lg font-bold text-desnky-dark">Explore projects across our sectors.</p>
            </div>
            <a href="/projects" class="btn-secondary">
                View all projects
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
            </a>
        </div>
    </div>
</section>
