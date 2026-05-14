<section class="relative isolate overflow-hidden bg-desnky-navy text-white">
    <img
        src="https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1800&q=80"
        alt="Industrial engineering worksite representing Desnky Global Resources services"
        class="absolute inset-0 -z-10 h-full w-full object-cover opacity-35"
        loading="eager"
    >
    <div class="absolute inset-0 -z-10 bg-desnky-navy/70"></div>
    <div class="container-page grid min-h-[calc(100vh-5rem)] content-center py-20 sm:py-24">
        <div class="max-w-4xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-desnky-gold">Desnky Global Resources Ltd</p>
            <h1 class="mt-5 max-w-5xl text-4xl font-bold leading-tight sm:text-6xl">
                Integrated Energy, Engineering, Procurement, Safety, ICT and Agro Solutions in Nigeria
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">
                We help organizations source materials, coordinate technical work, improve HSE readiness and support
                business operations across key industrial and commercial sectors.
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="/services" class="btn-primary bg-desnky-gold text-desnky-navy hover:bg-white">Learn More</a>
                <a href="/contact" class="btn-secondary border-white text-white hover:bg-white hover:text-desnky-navy">
                    Request a Quote
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue">What we do</p>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy">Services built around practical delivery</h2>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <article class="border border-gray-200 bg-white p-6 shadow-card">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-desnky-surface text-sm font-bold text-desnky-blue">
                        <?php echo $this->escape($service['icon']); ?>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-desnky-navy"><?php echo $this->escape($service['title']); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($service['summary']); ?></p>
                    <a href="/services/<?php echo $this->escape($service['slug']); ?>" class="mt-5 inline-flex text-sm font-semibold text-desnky-blue">
                        View More
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-band bg-desnky-surface">
    <div class="container-page grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-desnky-green">Why choose us</p>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy">A dependable partner for multi-sector operations</h2>
            <p class="mt-5 text-base leading-7 text-desnky-muted">
                Desnky Global Resources combines sector knowledge, vendor coordination and responsive communication so
                teams can keep procurement, technical and operational work moving.
            </p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <?php foreach (['Multi-sector experience', 'Safety-aware execution', 'Clear procurement follow-up', 'Responsive project support'] as $benefit) : ?>
                <div class="flex gap-4 bg-white p-5">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-desnky-green text-sm font-bold text-white">&check;</span>
                    <p class="font-semibold text-desnky-navy"><?php echo $this->escape($benefit); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_0.85fr] lg:items-center">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue">HSE commitment</p>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy">Safety is part of delivery, not an afterthought</h2>
            <p class="mt-5 text-base leading-7 text-desnky-muted">
                We support safe work through risk awareness, appropriate materials, practical planning and responsible
                execution across each engagement.
            </p>
            <a href="/hse-policy" class="mt-6 inline-flex font-semibold text-desnky-blue">Read our HSE policy</a>
        </div>
        <img
            src="https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=900&q=80"
            alt="Safety-focused industrial team planning site work"
            class="h-72 w-full object-cover"
            loading="lazy"
        >
    </div>
</section>

<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue">Featured projects</p>
                <h2 class="mt-3 text-3xl font-bold text-desnky-navy">Representative work highlights</h2>
            </div>
            <a href="/projects" class="btn-secondary">View Gallery</a>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php foreach ($projects as $project) : ?>
                <article class="bg-white shadow-card">
                    <img src="<?php echo $this->escape($project['image']); ?>" alt="<?php echo $this->escape($project['title']); ?>" class="h-52 w-full object-cover" loading="lazy">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-desnky-navy"><?php echo $this->escape($project['title']); ?></h3>
                        <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($project['summary']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page">
        <h2 class="text-center text-3xl font-bold text-desnky-navy">Our Trusted Clients</h2>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($clients as $client) : ?>
                <div class="border border-gray-200 px-5 py-6 text-center text-sm font-semibold text-desnky-muted">
                    <?php echo $this->escape($client); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-band bg-desnky-navy text-white">
    <div class="container-page grid gap-8 lg:grid-cols-2 lg:items-center">
        <div>
            <h2 class="text-3xl font-bold">Ready to work with us?</h2>
            <p class="mt-4 text-gray-200">Send your enquiry and our team will respond with the next practical step.</p>
        </div>
        <form class="grid gap-3 sm:grid-cols-[1fr_auto]" action="/newsletter" method="POST" data-newsletter-form>
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
            <label class="sr-only" for="newsletter_email">Email address</label>
            <input id="newsletter_email" name="email" type="email" required placeholder="Email address" class="form-field">
            <button type="submit" class="btn-primary bg-desnky-gold text-desnky-navy hover:bg-white">Subscribe</button>
        </form>
        <div class="lg:col-span-2">
            <a href="/contact" class="btn-secondary border-white text-white hover:bg-white hover:text-desnky-navy">Contact Us</a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('[data-newsletter-form]', {
        endpoint: '/newsletter',
        resetOnSuccess: true
    });
});
</script>
