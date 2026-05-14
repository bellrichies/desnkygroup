<?php
$project = $project ?? [];
$detail = $detail ?? [];
?>

<section class="relative isolate bg-desnky-navy text-white">
    <?php if (!empty($project['image'])) : ?>
        <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape((string) $project['title']); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-35" loading="eager">
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-desnky-navy/75"></div>
    <div class="container-page py-20">
        <nav class="text-sm text-gray-200" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? '')); ?></a> /
            <a href="/projects" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_projects_label'] ?? '')); ?></a> /
            <span><?php echo $this->escape((string) $project['title']); ?></span>
        </nav>
        <div class="mt-8 max-w-3xl">
            <?php if (!empty($project['category'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-gold"><?php echo $this->escape((string) $project['category']); ?></p>
            <?php endif; ?>
            <h1 class="mt-4 text-4xl font-bold sm:text-5xl"><?php echo $this->escape((string) $project['title']); ?></h1>
            <p class="mt-5 text-lg leading-8 text-gray-100"><?php echo $this->escape((string) $project['summary']); ?></p>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[1fr_22rem]">
        <div>
            <h2 class="text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['overview_heading'] ?? '')); ?></h2>
            <?php if (!empty($project['overview'])) : ?>
                <div class="prose mt-6 max-w-none text-desnky-muted">
                    <?php echo nl2br($this->escape((string) $project['overview'])); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['highlights'])) : ?>
                <h2 class="mt-12 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['highlights_heading'] ?? '')); ?></h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($project['highlights'] as $highlight) : ?>
                        <div class="border border-gray-200 p-5">
                            <span class="text-sm font-bold text-desnky-blue"><?php echo $this->escape((string) ($detail['highlight_label'] ?? '')); ?></span>
                            <p class="mt-2 font-semibold text-desnky-navy"><?php echo $this->escape((string) $highlight); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['outcomes'])) : ?>
                <h2 class="mt-12 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['outcomes_heading'] ?? '')); ?></h2>
                <ul class="mt-6 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($project['outcomes'] as $outcome) : ?>
                        <li class="flex gap-3 bg-desnky-surface p-5 text-sm font-semibold text-desnky-navy">
                            <span class="font-bold text-desnky-green">&check;</span>
                            <?php echo $this->escape((string) $outcome); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <aside class="bg-desnky-surface p-6">
            <h2 class="text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['summary_heading'] ?? '')); ?></h2>
            <dl class="mt-5 space-y-4 text-sm">
                <?php if (!empty($project['category'])) : ?>
                    <div>
                        <dt class="font-bold text-desnky-blue"><?php echo $this->escape((string) ($detail['category_label'] ?? '')); ?></dt>
                        <dd class="mt-1 text-desnky-muted"><?php echo $this->escape((string) $project['category']); ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($project['client_name'])) : ?>
                    <div>
                        <dt class="font-bold text-desnky-blue"><?php echo $this->escape((string) ($detail['client_label'] ?? '')); ?></dt>
                        <dd class="mt-1 text-desnky-muted"><?php echo $this->escape((string) $project['client_name']); ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($project['project_date'])) : ?>
                    <div>
                        <dt class="font-bold text-desnky-blue"><?php echo $this->escape((string) ($detail['date_label'] ?? '')); ?></dt>
                        <dd class="mt-1 text-desnky-muted"><?php echo $this->escape($this->formatDate((string) $project['project_date'])); ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
            <?php if (!empty($detail['cta_label']) && !empty($detail['cta_url'])) : ?>
                <a href="<?php echo $this->escape((string) $detail['cta_url']); ?>" class="btn-primary mt-6 w-full">
                    <?php echo $this->escape((string) $detail['cta_label']); ?>
                </a>
            <?php endif; ?>
        </aside>
    </div>
</section>
