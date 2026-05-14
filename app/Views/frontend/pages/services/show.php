<section class="relative isolate bg-desnky-navy text-white">
    <img src="<?php echo $this->escape($service['image']); ?>" alt="<?php echo $this->escape($service['title']); ?>" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-35" loading="eager">
    <div class="absolute inset-0 -z-10 bg-desnky-navy/75"></div>
    <div class="container-page py-20">
        <nav class="text-sm text-gray-200" aria-label="Breadcrumb">
            <a href="/" class="hover:text-white">Home</a> /
            <a href="/services" class="hover:text-white">Services</a> /
            <span><?php echo $this->escape($service['title']); ?></span>
        </nav>
        <div class="mt-8 max-w-3xl">
            <h1 class="text-4xl font-bold sm:text-5xl"><?php echo $this->escape($service['title']); ?></h1>
            <p class="mt-5 text-lg leading-8 text-gray-100"><?php echo $this->escape($service['summary']); ?></p>
            <a href="/contact" class="btn-primary mt-8 bg-desnky-gold text-desnky-navy hover:bg-white">Discuss This Service</a>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[1fr_22rem]">
        <div>
            <h2 class="text-3xl font-bold text-desnky-navy">Service features</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <?php foreach ($service['features'] as $feature) : ?>
                    <div class="border border-gray-200 p-5">
                        <span class="text-sm font-bold text-desnky-blue">Feature</span>
                        <p class="mt-2 font-semibold text-desnky-navy"><?php echo $this->escape($feature); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <h2 class="mt-12 text-3xl font-bold text-desnky-navy">Our process</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <?php foreach ($service['process'] as $index => $step) : ?>
                    <div class="bg-desnky-surface p-5">
                        <span class="text-sm font-bold text-desnky-blue">0<?php echo $index + 1; ?></span>
                        <p class="mt-3 font-semibold text-desnky-navy"><?php echo $this->escape($step); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="bg-desnky-surface p-6">
            <h2 class="text-xl font-bold text-desnky-navy">Why choose this service</h2>
            <ul class="mt-5 space-y-3 text-sm leading-6 text-desnky-muted">
                <?php foreach ($service['benefits'] as $benefit) : ?>
                    <li class="flex gap-3"><span class="font-bold text-desnky-green">&check;</span><?php echo $this->escape($benefit); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="/contact" class="btn-primary mt-6 w-full">Request a Quote</a>
        </aside>
    </div>
</section>

<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h2 class="text-3xl font-bold text-desnky-navy">Related services</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            <?php foreach (array_slice($relatedServices, 0, 3) as $related) : ?>
                <a href="/services/<?php echo $this->escape($related['slug']); ?>" class="block bg-white p-6 shadow-card hover:text-desnky-blue">
                    <span class="text-sm font-bold text-desnky-blue"><?php echo $this->escape($related['icon']); ?></span>
                    <h3 class="mt-3 text-lg font-bold text-desnky-navy"><?php echo $this->escape($related['title']); ?></h3>
                    <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($related['summary']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
