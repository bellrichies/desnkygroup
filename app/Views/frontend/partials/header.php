<header class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur">
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:px-4 focus:py-2"
    >
        Skip to content
    </a>

    <div class="container-page">
        <div class="flex min-h-20 items-center justify-between gap-6">
            <a href="/" class="flex items-center gap-3" aria-label="Desnky Global Resources home">
                <span class="flex h-11 w-11 items-center justify-center rounded-md bg-desnky-navy text-lg font-bold text-white">
                    DG
                </span>
                <span class="leading-tight">
                    <span class="block text-base font-bold text-desnky-navy">Desnky Global</span>
                    <span class="block text-xs font-medium text-desnky-muted">Resources Ltd</span>
                </span>
            </a>

            <?php echo $this->partial('frontend/partials/navigation', ['active' => $active ?? '']); ?>

            <a href="/contact" class="hidden btn-primary lg:inline-flex">Request a Quote</a>
        </div>
    </div>
</header>
