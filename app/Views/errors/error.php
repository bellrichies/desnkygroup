<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="max-w-2xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue">
                Error <?php echo $this->escape((string) ($status ?? 500)); ?>
            </p>
            <h1 class="mt-3 text-4xl font-bold text-desnky-navy">
                <?php echo ((int) ($status ?? 500)) === 404 ? 'Page not found' : 'Something went wrong'; ?>
            </h1>
            <p class="mt-5 text-lg leading-8 text-desnky-muted">
                <?php echo $this->escape($message ?? 'An unexpected error occurred.'); ?>
            </p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="/" class="btn-primary">Go Home</a>
                <a href="/contact" class="btn-secondary">Contact Us</a>
            </div>
        </div>
    </div>
</section>
