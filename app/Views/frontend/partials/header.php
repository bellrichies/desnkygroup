<?php
$isHomeHeader = ($active ?? '') === 'home';
$cartCount = array_sum(array_map('intval', $_SESSION['cart'] ?? []));
$site = $site ?? [];
$brandName = (string) ($site['name'] ?? 'Desnky Global Resources Ltd');
?>
<header
    x-data="{
        mobileOpen: false,
        servicesOpen: false,
        servicesCloseTimer: null,
        openServices() {
            window.clearTimeout(this.servicesCloseTimer);
            this.servicesCloseTimer = null;
            this.servicesOpen = true;
        },
        closeServices() {
            window.clearTimeout(this.servicesCloseTimer);
            this.servicesCloseTimer = null;
            this.servicesOpen = false;
        },
        scheduleServicesClose() {
            window.clearTimeout(this.servicesCloseTimer);
            this.servicesCloseTimer = window.setTimeout(() => {
                this.servicesOpen = false;
                this.servicesCloseTimer = null;
            }, 180);
        }
    }"
    @keydown.escape.window="mobileOpen = false; closeServices()"
    x-effect="document.body.style.overflow = mobileOpen ? 'hidden' : ''"
    class="<?php echo $isHomeHeader
        ? 'site-header site-header--home site-header--transparent top-0 z-header border-b'
        : 'site-header sticky top-0 z-header border-b border-gray-200 bg-white/95 backdrop-blur'; ?>"
    <?php echo $isHomeHeader ? 'data-home-header' : ''; ?>
>
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-desnky-primary focus:shadow-card"
    >
        Skip to content
    </a>

    <div class="container-page">
        <div class="flex min-h-20 items-center justify-between gap-4">
            <a href="/" class="site-header__brand flex items-center gap-3" aria-label="<?php echo $this->escape($brandName); ?> home">
                <span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-md bg-white p-1 shadow-sm ring-1 ring-black/5">
                    <img src="/assets/images/logo.png" alt="" class="h-full w-full object-contain" width="44" height="44">
                </span>
                <span class="leading-tight">
                    <span class="site-header__name block text-base font-bold text-desnky-dark">Desnky Global</span>
                    <span class="site-header__meta block text-xs font-medium text-desnky-muted">Resources Ltd</span>
                </span>
            </a>

            <?php echo $this->partial('frontend/partials/navigation', ['active' => $active ?? '']); ?>

            <div class="flex items-center gap-2 sm:gap-3">
                <a
                    href="/shop/cart"
                    class="site-header__nav-link relative inline-flex h-11 w-11 items-center justify-center rounded-md text-desnky-ink transition-colors hover:bg-desnky-surface focus:outline-none focus-visible:ring-2 focus-visible:ring-desnky-primary"
                    aria-label="View cart<?php echo $cartCount > 0 ? ', ' . $cartCount . ' item' . ($cartCount === 1 ? '' : 's') : ''; ?>"
                >
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'cart', 'class' => 'h-6 w-6']); ?>
                    <span
                        data-cart-count
                        class="absolute -right-0.5 -top-0.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-desnky-secondary px-1 text-[0.65rem] font-bold text-white <?php echo $cartCount > 0 ? '' : 'hidden'; ?>"
                    ><?php echo (int) $cartCount; ?></span>
                </a>

                <a href="/contact" class="hidden btn-primary lg:inline-flex">Request a Quote</a>

                <button
                    type="button"
                    class="site-header__menu-button btn-icon lg:hidden"
                    @click="mobileOpen = true"
                    :aria-expanded="mobileOpen.toString()"
                    aria-controls="mobile-navigation"
                >
                    <span class="sr-only">Open navigation menu</span>
                    <?php echo $this->partial('frontend/partials/icon', ['name' => 'menu', 'class' => 'h-6 w-6']); ?>
                </button>
            </div>
        </div>
    </div>
</header>
