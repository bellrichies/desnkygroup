<?php
$hero = $hero ?? [];
$listing = $listing ?? [];
$services = $services ?? [];
?>

<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <nav class="text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue"><?php echo $this->escape((string) ($hero['breadcrumb_home_label'] ?? '')); ?></a> /
            <span><?php echo $this->escape((string) ($hero['breadcrumb_current_label'] ?? $hero['heading'] ?? '')); ?></span>
        </nav>
        <div class="mt-6 max-w-3xl">
            <h1 class="text-4xl font-bold text-desnky-navy sm:text-5xl"><?php echo $this->escape((string) ($hero['heading'] ?? '')); ?></h1>
            <?php if (!empty($hero['text'])) : ?>
                <p class="mt-5 text-lg leading-8 text-desnky-muted">
                    <?php echo $this->escape((string) $hero['text']); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($services !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="mb-8 flex flex-wrap gap-3" aria-label="<?php echo $this->escape((string) ($listing['filters_label'] ?? '')); ?>">
            <a href="/services" class="rounded-md bg-desnky-navy px-4 py-2 text-sm font-semibold text-white">
                <?php echo $this->escape((string) ($listing['all_services_label'] ?? '')); ?>
            </a>
            <?php foreach ($services as $service) : ?>
                <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-desnky-navy hover:border-desnky-blue hover:text-desnky-blue">
                    <?php echo $this->escape((string) $service['title']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <article class="overflow-hidden border border-gray-200 bg-white shadow-card">
                    <?php if (!empty($service['image'])) : ?>
                        <img src="<?php echo $this->escape((string) $service['image']); ?>" alt="<?php echo $this->escape((string) $service['title']); ?>" class="h-48 w-full object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-md bg-desnky-surface text-sm font-bold text-desnky-blue">
                            <?php echo $this->escape((string) $service['icon']); ?>
                        </div>
                        <h2 class="mt-5 text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) $service['title']); ?></h2>
                        <p class="mt-3 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $service['summary']); ?></p>
                        <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="btn-primary mt-5">
                            <?php echo $this->escape((string) ($listing['card_cta_label'] ?? '')); ?>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
