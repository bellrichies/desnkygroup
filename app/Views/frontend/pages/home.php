<?php
$hero = $hero ?? [];
$servicesIntro = $servicesIntro ?? [];
$whyChooseUs = $whyChooseUs ?? [];
$hseCommitment = $hseCommitment ?? [];
$projectsIntro = $projectsIntro ?? [];
$clientsSection = $clientsSection ?? [];
$cta = $cta ?? [];
$services = $services ?? [];
$projects = $projects ?? [];
$clients = $clientsSection['items'] ?? [];
?>

<?php if ($hero !== []) : ?>
<section class="relative isolate overflow-hidden bg-desnky-navy text-white">
    <?php if (!empty($hero['image'])) : ?>
        <img
            src="<?php echo $this->escape((string) $hero['image']); ?>"
            alt="<?php echo $this->escape((string) ($hero['image_alt'] ?? $hero['heading'] ?? '')); ?>"
            class="absolute inset-0 -z-10 h-full w-full object-cover opacity-35"
            loading="eager"
        >
    <?php endif; ?>
    <div class="absolute inset-0 -z-10 bg-desnky-navy/70"></div>
    <div class="container-page grid min-h-[calc(100vh-5rem)] content-center py-20 sm:py-24">
        <div class="max-w-4xl">
            <?php if (!empty($hero['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-gold"><?php echo $this->escape((string) $hero['eyebrow']); ?></p>
            <?php endif; ?>
            <h1 class="mt-5 max-w-5xl text-4xl font-bold leading-tight sm:text-6xl">
                <?php echo $this->escape((string) ($hero['heading'] ?? '')); ?>
            </h1>
            <?php if (!empty($hero['text'])) : ?>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-100">
                    <?php echo $this->escape((string) $hero['text']); ?>
                </p>
            <?php endif; ?>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <?php if (!empty($hero['primary_cta_label']) && !empty($hero['primary_cta_url'])) : ?>
                    <a href="<?php echo $this->escape((string) $hero['primary_cta_url']); ?>" class="btn-primary bg-desnky-gold text-desnky-navy hover:bg-white">
                        <?php echo $this->escape((string) $hero['primary_cta_label']); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($hero['secondary_cta_label']) && !empty($hero['secondary_cta_url'])) : ?>
                    <a href="<?php echo $this->escape((string) $hero['secondary_cta_url']); ?>" class="btn-secondary border-white text-white hover:bg-white hover:text-desnky-navy">
                        <?php echo $this->escape((string) $hero['secondary_cta_label']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($servicesIntro !== [] && $services !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <div class="max-w-3xl">
            <?php if (!empty($servicesIntro['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $servicesIntro['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($servicesIntro['heading'] ?? '')); ?></h2>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($services as $service) : ?>
                <article class="border border-gray-200 bg-white p-6 shadow-card">
                    <div class="flex h-12 w-12 items-center justify-center rounded-md bg-desnky-surface text-sm font-bold text-desnky-blue">
                        <?php echo $this->escape((string) $service['icon']); ?>
                    </div>
                    <h3 class="mt-5 text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) $service['title']); ?></h3>
                    <p class="mt-3 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $service['summary']); ?></p>
                    <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" class="mt-5 inline-flex text-sm font-semibold text-desnky-blue">
                        <?php echo $this->escape((string) ($servicesIntro['item_cta_label'] ?? '')); ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($whyChooseUs !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
        <div>
            <?php if (!empty($whyChooseUs['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-green"><?php echo $this->escape((string) $whyChooseUs['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($whyChooseUs['heading'] ?? '')); ?></h2>
            <?php if (!empty($whyChooseUs['text'])) : ?>
                <p class="mt-5 text-base leading-7 text-desnky-muted"><?php echo $this->escape((string) $whyChooseUs['text']); ?></p>
            <?php endif; ?>
        </div>
        <?php if (!empty($whyChooseUs['benefits']) && is_array($whyChooseUs['benefits'])) : ?>
            <div class="grid gap-4 sm:grid-cols-2">
                <?php foreach ($whyChooseUs['benefits'] as $benefit) : ?>
                    <div class="flex gap-4 bg-white p-5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md bg-desnky-green text-sm font-bold text-white">&check;</span>
                        <p class="font-semibold text-desnky-navy"><?php echo $this->escape((string) $benefit); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($hseCommitment !== []) : ?>
<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_0.85fr] lg:items-center">
        <div>
            <?php if (!empty($hseCommitment['eyebrow'])) : ?>
                <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $hseCommitment['eyebrow']); ?></p>
            <?php endif; ?>
            <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($hseCommitment['heading'] ?? '')); ?></h2>
            <?php if (!empty($hseCommitment['text'])) : ?>
                <p class="mt-5 text-base leading-7 text-desnky-muted"><?php echo $this->escape((string) $hseCommitment['text']); ?></p>
            <?php endif; ?>
            <?php if (!empty($hseCommitment['cta_label']) && !empty($hseCommitment['cta_url'])) : ?>
                <a href="<?php echo $this->escape((string) $hseCommitment['cta_url']); ?>" class="mt-6 inline-flex font-semibold text-desnky-blue">
                    <?php echo $this->escape((string) $hseCommitment['cta_label']); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($hseCommitment['image'])) : ?>
            <img
                src="<?php echo $this->escape((string) $hseCommitment['image']); ?>"
                alt="<?php echo $this->escape((string) ($hseCommitment['image_alt'] ?? $hseCommitment['heading'] ?? '')); ?>"
                class="h-72 w-full object-cover"
                loading="lazy"
            >
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($projectsIntro !== [] && $projects !== []) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
            <div>
                <?php if (!empty($projectsIntro['eyebrow'])) : ?>
                    <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $projectsIntro['eyebrow']); ?></p>
                <?php endif; ?>
                <h2 class="mt-3 text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($projectsIntro['heading'] ?? '')); ?></h2>
            </div>
            <?php if (!empty($projectsIntro['cta_label']) && !empty($projectsIntro['cta_url'])) : ?>
                <a href="<?php echo $this->escape((string) $projectsIntro['cta_url']); ?>" class="btn-secondary">
                    <?php echo $this->escape((string) $projectsIntro['cta_label']); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <?php foreach ($projects as $project) : ?>
                <article class="bg-white shadow-card">
                    <?php if (!empty($project['image'])) : ?>
                        <img src="<?php echo $this->escape((string) $project['image']); ?>" alt="<?php echo $this->escape((string) $project['title']); ?>" class="h-52 w-full object-cover" loading="lazy">
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-desnky-navy"><?php echo $this->escape((string) $project['title']); ?></h3>
                        <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $project['summary']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($clientsSection !== [] && is_array($clients) && $clients !== []) : ?>
<section class="section-band">
    <div class="container-page">
        <h2 class="text-center text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($clientsSection['heading'] ?? '')); ?></h2>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <?php foreach ($clients as $client) : ?>
                <div class="border border-gray-200 px-5 py-6 text-center text-sm font-semibold text-desnky-muted">
                    <?php echo $this->escape((string) $client); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($cta !== []) : ?>
<section class="section-band bg-desnky-navy text-white">
    <div class="container-page grid gap-8 lg:grid-cols-2 lg:items-center">
        <div>
            <h2 class="text-3xl font-bold"><?php echo $this->escape((string) ($cta['heading'] ?? '')); ?></h2>
            <?php if (!empty($cta['text'])) : ?>
                <p class="mt-4 text-gray-200"><?php echo $this->escape((string) $cta['text']); ?></p>
            <?php endif; ?>
        </div>
        <form class="grid gap-3 sm:grid-cols-[1fr_auto]" action="/newsletter" method="POST" data-newsletter-form>
            <input type="hidden" name="_token" value="<?php echo $this->escape((string) $csrf_token); ?>">
            <label class="sr-only" for="newsletter_email"><?php echo $this->escape((string) ($cta['email_label'] ?? '')); ?></label>
            <input id="newsletter_email" name="email" type="email" required placeholder="<?php echo $this->escape((string) ($cta['email_placeholder'] ?? '')); ?>" class="form-field">
            <button type="submit" class="btn-primary bg-desnky-gold text-desnky-navy hover:bg-white">
                <?php echo $this->escape((string) ($cta['newsletter_button_label'] ?? '')); ?>
            </button>
        </form>
        <?php if (!empty($cta['contact_label']) && !empty($cta['contact_url'])) : ?>
            <div class="lg:col-span-2">
                <a href="<?php echo $this->escape((string) $cta['contact_url']); ?>" class="btn-secondary border-white text-white hover:bg-white hover:text-desnky-navy">
                    <?php echo $this->escape((string) $cta['contact_label']); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('[data-newsletter-form]', {
        endpoint: '/newsletter',
        resetOnSuccess: true
    });
});
</script>


