<?php
$service = $service ?? [];
$detail = $detail ?? [];
$relatedServices = $relatedServices ?? [];
?>

<section class="relative isolate bg-desnky-navy text-white">
    <?php if (!empty($service['image'])) : ?>
        <img src="<?php echo $this->escape((string) $service['image']); ?>" alt="<?php echo $this->escape((string) $service['title']); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-35" loading="eager">
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-desnky-navy/75"></div>
    <div class="container-page py-20">
        <nav class="text-sm text-gray-200" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? '')); ?></a> /
            <a href="/services" class="hover:text-white"><?php echo $this->escape((string) ($detail['breadcrumb_services_label'] ?? '')); ?></a> /
            <span><?php echo $this->escape((string) $service['title']); ?></span>
        </nav>
        <div class="mt-8 max-w-3xl">
            <h1 class="text-4xl font-bold sm:text-5xl"><?php echo $this->escape((string) $service['title']); ?></h1>
            <p class="mt-5 text-lg leading-8 text-gray-100"><?php echo $this->escape((string) $service['summary']); ?></p>
            <?php if (!empty($detail['primary_cta_label']) && !empty($detail['primary_cta_url'])) : ?>
                <a href="<?php echo $this->escape((string) $detail['primary_cta_url']); ?>" class="btn-primary mt-8 bg-desnky-gold text-desnky-navy hover:bg-white">
                    <?php echo $this->escape((string) $detail['primary_cta_label']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[1fr_22rem]">
        <div>
            <h2 class="text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['features_heading'] ?? '')); ?></h2>
            <?php if (!empty($service['overview'])) : ?>
                <div class="prose mt-6 max-w-none text-desnky-muted">
                    <?php echo nl2br($this->escape((string) $service['overview'])); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($service['features'])) : ?>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($service['features'] as $feature) : ?>
                        <div class="border border-gray-200 p-5">
                            <span class="text-sm font-bold text-desnky-blue"><?php echo $this->escape((string) ($detail['feature_label'] ?? '')); ?></span>
                            <p class="mt-2 font-semibold text-desnky-navy"><?php echo $this->escape((string) $feature); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($service['process'])) : ?>
                <h2 class="mt-12 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['process_heading'] ?? '')); ?></h2>
                <div class="mt-6 grid gap-4 md:grid-cols-4">
                    <?php foreach ($service['process'] as $index => $step) : ?>
                        <div class="bg-desnky-surface p-5">
                            <span class="text-sm font-bold text-desnky-blue"><?php echo str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT); ?></span>
                            <p class="mt-3 font-semibold text-desnky-navy"><?php echo $this->escape((string) $step); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($service['benefits'])) : ?>
            <aside class="bg-desnky-surface p-6">
                <h2 class="text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['benefits_heading'] ?? '')); ?></h2>
                <ul class="mt-5 space-y-3 text-sm leading-6 text-desnky-muted">
                    <?php foreach ($service['benefits'] as $benefit) : ?>
                        <li class="flex gap-3"><span class="font-bold text-desnky-green">&check;</span><?php echo $this->escape((string) $benefit); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if (!empty($detail['secondary_cta_label']) && !empty($detail['secondary_cta_url'])) : ?>
                    <a href="<?php echo $this->escape((string) $detail['secondary_cta_url']); ?>" class="btn-primary mt-6 w-full">
                        <?php echo $this->escape((string) $detail['secondary_cta_label']); ?>
                    </a>
                <?php endif; ?>
            </aside>
        <?php endif; ?>
    </div>
</section>

<?php if ($relatedServices !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h2 class="text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['related_heading'] ?? '')); ?></h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <?php foreach (array_slice($relatedServices, 0, 3) as $related) : ?>
                <a href="/services/<?php echo $this->escape((string) $related['slug']); ?>" class="block bg-white p-6 shadow-card hover:text-desnky-blue">
                    <span class="text-sm font-bold text-desnky-blue"><?php echo $this->escape((string) $related['icon']); ?></span>
                    <h3 class="mt-3 text-lg font-bold text-desnky-navy"><?php echo $this->escape((string) $related['title']); ?></h3>
                    <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $related['summary']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
