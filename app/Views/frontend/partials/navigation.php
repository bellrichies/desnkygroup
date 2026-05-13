<?php
$items = [
    ['label' => 'Home', 'href' => '/', 'key' => 'home'],
    ['label' => 'Services', 'href' => '/services', 'key' => 'services'],
    ['label' => 'Projects', 'href' => '/projects', 'key' => 'projects'],
    ['label' => 'Contact', 'href' => '/contact', 'key' => 'contact'],
];
?>

<nav class="flex items-center" aria-label="Primary navigation" x-data="{ open: false }">
    <button
        type="button"
        class="inline-flex rounded-md p-2 text-desnky-navy hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-desnky-blue lg:hidden"
        aria-controls="mobile-navigation"
        aria-expanded="false"
        data-mobile-menu-button
    >
        <span class="sr-only">Open navigation</span>
        <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
        </svg>
    </button>

    <div class="hidden items-center gap-7 lg:flex">
        <?php foreach ($items as $item) : ?>
            <?php $isActive = ($active ?? '') === $item['key']; ?>
            <a
                href="<?php echo $this->escape($item['href']); ?>"
                class="<?php echo $isActive ? 'text-desnky-blue' : 'text-desnky-ink hover:text-desnky-blue'; ?> text-sm font-semibold"
                <?php echo $isActive ? 'aria-current="page"' : ''; ?>
            >
                <?php echo $this->escape($item['label']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div
        id="mobile-navigation"
        class="absolute left-0 right-0 top-20 hidden border-b border-gray-200 bg-white px-4 py-4 shadow-card lg:hidden"
        data-mobile-menu
    >
        <div class="grid gap-2">
            <?php foreach ($items as $item) : ?>
                <?php $isActive = ($active ?? '') === $item['key']; ?>
                <a
                    href="<?php echo $this->escape($item['href']); ?>"
                    class="<?php echo $isActive ? 'bg-desnky-surface text-desnky-blue' : 'text-desnky-ink'; ?> rounded-md px-3 py-3 text-sm font-semibold"
                    <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                >
                    <?php echo $this->escape($item['label']); ?>
                </a>
            <?php endforeach; ?>
            <a href="/contact" class="btn-primary mt-2">Request a Quote</a>
        </div>
    </div>
</nav>
