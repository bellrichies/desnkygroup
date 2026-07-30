<?php
$active = $active ?? '';

$items = [
    ['label' => 'Home', 'href' => '/', 'key' => 'home'],
    ['label' => 'About', 'href' => '/about', 'key' => 'about'],
    ['label' => 'Services', 'href' => '/services', 'key' => 'services'],
    ['label' => 'Projects', 'href' => '/projects', 'key' => 'projects'],
    ['label' => 'Shop', 'href' => '/shop', 'key' => 'shop'],
    ['label' => 'Blog', 'href' => '/blog', 'key' => 'blog'],
    ['label' => 'Contact', 'href' => '/contact', 'key' => 'contact'],
];

// Six sector spokes for the Services mega-menu / mobile accordion.
$sectors = [
    ['label' => 'Engineering Services', 'href' => '/services/engineering', 'icon' => 'cog', 'desc' => 'Electrical, mechanical & civil project delivery.'],
    ['label' => 'Energy Solutions', 'href' => '/services/energy-solutions', 'icon' => 'bolt', 'desc' => 'Power, oil & gas and renewable energy support.'],
    ['label' => 'Procurement Services', 'href' => '/services/procurement', 'icon' => 'truck', 'desc' => 'Sourcing, logistics & supply-chain management.'],
    ['label' => 'Fire Safety / HSE Services', 'href' => '/services/hse-safety', 'icon' => 'shield-check', 'desc' => 'HSE consulting, training & safety equipment.'],
    ['label' => 'ICT Solutions', 'href' => '/services/ict-solutions', 'icon' => 'server', 'desc' => 'Infrastructure, networks & digital systems.'],
    ['label' => 'Agro Products & Food', 'href' => '/services/agro-food-processing', 'icon' => 'leaf', 'desc' => 'Agro products & food-processing solutions.'],
];

$site = $site ?? [];
$phone = (string) ($site['phone'] ?? '+2340000000000');
$phoneDisplay = (string) ($site['phone_display'] ?? $phone);
$whatsapp = (string) ($site['whatsapp'] ?? '2340000000000');
?>

<nav class="hidden items-center gap-7 lg:flex" aria-label="Primary">
    <?php foreach ($items as $item) : ?>
        <?php $isActive = $active === $item['key']; ?>
        <?php if ($item['key'] === 'services') : ?>
            <div class="relative" @mouseenter="servicesOpen = true" @mouseleave="servicesOpen = false">
                <a
                    href="/services"
                    class="site-header__nav-link nav-link inline-flex items-center gap-1 <?php echo $isActive ? 'nav-link-active' : ''; ?>"
                    @click="servicesOpen = !servicesOpen"
                    @keydown.escape="servicesOpen = false"
                    :aria-expanded="servicesOpen.toString()"
                    aria-haspopup="true"
                    <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                >
                    Services
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4 transition-transform']); ?>
                </a>
                <div
                    x-show="servicesOpen"
                    x-transition.opacity.duration.200ms
                    x-cloak
                    class="mega-panel"
                    role="menu"
                    aria-label="Services"
                    style="display: none;"
                >
                    <div class="grid gap-1 sm:grid-cols-2">
                        <?php foreach ($sectors as $sector) : ?>
                            <a href="<?php echo $this->escape($sector['href']); ?>" class="mega-item" role="menuitem">
                                <span class="icon-tile">
                                    <?php echo $this->partial('frontend/partials/icon', ['name' => $sector['icon'], 'class' => 'h-6 w-6']); ?>
                                </span>
                                <span>
                                    <span class="block text-sm font-bold text-desnky-dark"><?php echo $this->escape($sector['label']); ?></span>
                                    <span class="mt-0.5 block text-xs leading-5 text-desnky-muted"><?php echo $this->escape($sector['desc']); ?></span>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
                        <a href="/services" class="btn-ghost">
                            View all services
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
                        </a>
                        <a href="/contact" class="btn-primary">Discuss a project</a>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <a
                href="<?php echo $this->escape($item['href']); ?>"
                class="site-header__nav-link nav-link <?php echo $isActive ? 'nav-link-active' : ''; ?>"
                <?php echo $isActive ? 'aria-current="page"' : ''; ?>
            >
                <?php echo $this->escape($item['label']); ?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>

<!-- Mobile off-canvas drawer (shares header Alpine scope: mobileOpen, servicesOpen) -->
<div x-cloak x-show="mobileOpen" class="lg:hidden">
    <div
        x-show="mobileOpen"
        x-transition.opacity
        @click="mobileOpen = false"
        class="fixed inset-0 z-header bg-desnky-dark/50 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    <div
        id="mobile-navigation"
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-base"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[60] flex w-[min(22rem,calc(100vw-3rem))] flex-col bg-white shadow-2xl"
        role="dialog"
        aria-modal="true"
        aria-label="Site navigation"
    >
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <span class="text-base font-bold text-desnky-dark">Menu</span>
            <button type="button" class="btn-icon" @click="mobileOpen = false" x-init="$watch('mobileOpen', v => v && $nextTick(() => $el.focus()))">
                <span class="sr-only">Close navigation menu</span>
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'close', 'class' => 'h-6 w-6']); ?>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-4">
            <div class="grid gap-1">
                <?php foreach ($items as $item) : ?>
                    <?php $isActive = $active === $item['key']; ?>
                    <?php if ($item['key'] === 'services') : ?>
                        <div>
                            <button
                                type="button"
                                class="drawer-link flex w-full items-center justify-between"
                                @click="servicesOpen = !servicesOpen"
                                :aria-expanded="servicesOpen.toString()"
                            >
                                Services
                                <span :class="servicesOpen && 'rotate-180'" class="transition-transform">
                                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-5 w-5']); ?>
                                </span>
                            </button>
                            <div x-show="servicesOpen" x-collapse x-cloak class="ml-3 mt-1 grid gap-1 border-l border-gray-200 pl-3">
                                <a href="/services" class="block rounded-md px-3 py-2 text-sm font-semibold text-desnky-primary hover:bg-desnky-surface">All services</a>
                                <?php foreach ($sectors as $sector) : ?>
                                    <a href="<?php echo $this->escape($sector['href']); ?>" class="block rounded-md px-3 py-2 text-sm font-medium text-desnky-ink hover:bg-desnky-surface">
                                        <?php echo $this->escape($sector['label']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <a
                            href="<?php echo $this->escape($item['href']); ?>"
                            class="drawer-link <?php echo $isActive ? 'bg-desnky-surface text-desnky-primary' : ''; ?>"
                            <?php echo $isActive ? 'aria-current="page"' : ''; ?>
                        >
                            <?php echo $this->escape($item['label']); ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="space-y-3 border-t border-gray-200 px-5 py-4">
            <a href="/contact" class="btn-primary w-full">Request a Quote</a>
            <div class="grid grid-cols-2 gap-3">
                <a href="tel:<?php echo $this->escape($phone); ?>" class="btn-secondary">
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'phone', 'class' => 'h-5 w-5']); ?>
                    Call
                </a>
                <a href="https://wa.me/<?php echo $this->escape($whatsapp); ?>" class="btn-secondary" rel="noopener" target="_blank">
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'whatsapp', 'class' => 'h-5 w-5']); ?>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
