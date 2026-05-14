<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <nav class="text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue">Home</a> / <span>Services</span>
        </nav>
        <div class="mt-6 max-w-3xl">
            <h1 class="text-4xl font-bold text-desnky-navy sm:text-5xl">Our Services</h1>
            <p class="mt-5 text-lg leading-8 text-desnky-muted">
                Explore our core service areas across engineering, energy, procurement, HSE, ICT and agro food
                processing.
            </p>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page">
        <div class="mb-8 flex flex-wrap gap-3" aria-label="Service categories">
            <a href="/services" class="rounded-md bg-desnky-navy px-4 py-2 text-sm font-semibold text-white">All Services</a>
            <?php foreach ($services as $service) : ?>
                <a href="/services/<?php echo $this->escape($service['slug']); ?>" class="rounded-md border border-gray-200 px-4 py-2 text-sm font-semibold text-desnky-navy hover:border-desnky-blue hover:text-desnky-blue">
                    <?php echo $this->escape($service['title']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <article class="overflow-hidden border border-gray-200 bg-white shadow-card">
                    <img src="<?php echo $this->escape($service['image']); ?>" alt="<?php echo $this->escape($service['title']); ?>" class="h-48 w-full object-cover" loading="lazy">
                    <div class="p-6">
                        <div class="flex h-11 w-11 items-center justify-center rounded-md bg-desnky-surface text-sm font-bold text-desnky-blue">
                            <?php echo $this->escape($service['icon']); ?>
                        </div>
                        <h2 class="mt-5 text-xl font-bold text-desnky-navy"><?php echo $this->escape($service['title']); ?></h2>
                        <p class="mt-3 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($service['summary']); ?></p>
                        <a href="/services/<?php echo $this->escape($service['slug']); ?>" class="btn-primary mt-5">Learn More</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
