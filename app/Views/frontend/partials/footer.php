<?php
$year = date('Y');
$site = $site ?? [];
$brandName = (string) ($site['name'] ?? 'Desnky Global Resources Ltd');
$phone = (string) ($site['phone'] ?? '+2340000000000');
$phoneDisplay = (string) ($site['phone_display'] ?? $phone);
$email = (string) ($site['email'] ?? 'info@desnkygroup.com');
$address = (string) ($site['address'] ?? 'Lagos, Nigeria');
$csrf = (string) ($csrf_token ?? '');

$socialMeta = [
    'linkedin' => ['name' => 'linkedin', 'label' => 'LinkedIn'],
    'x' => ['name' => 'x-twitter', 'label' => 'X (Twitter)'],
    'facebook' => ['name' => 'facebook', 'label' => 'Facebook'],
    'instagram' => ['name' => 'instagram', 'label' => 'Instagram'],
];
$socials = [];
foreach (($site['social'] ?? []) as $key => $href) {
    if (!empty($href) && isset($socialMeta[$key])) {
        $socials[] = $socialMeta[$key] + ['href' => (string) $href];
    }
}
?>
<footer class="border-t-4 border-desnky-primary bg-desnky-darker text-gray-300">
    <div class="container-page py-14">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12">
            <!-- Brand + newsletter -->
            <div class="lg:col-span-4">
                <a href="/" class="flex items-center gap-3" aria-label="<?php echo $this->escape($brandName); ?> home">
                    <span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-md bg-white p-1 ring-1 ring-white/20">
                        <img src="/assets/images/logo.png" alt="" class="h-full w-full object-contain" width="44" height="44">
                    </span>
                    <span class="text-lg font-bold text-white"><?php echo $this->escape($brandName); ?></span>
                </a>
                <p class="mt-4 max-w-sm text-sm leading-6 text-gray-400">
                    Integrated energy, engineering, procurement, safety, ICT and agro solutions — delivered with industrial confidence and HSE-first discipline.
                </p>

                <form class="mt-6 max-w-sm" action="/newsletter" method="POST" data-newsletter-form aria-label="Newsletter signup">
                    <label for="footer_newsletter_email" class="text-sm font-semibold text-white">Get industry insights</label>
                    <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <input
                            id="footer_newsletter_email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="you@company.com"
                            class="w-full rounded-md border-0 bg-white/10 px-4 py-2.5 text-sm text-white placeholder:text-gray-400 ring-1 ring-inset ring-white/15 focus:ring-2 focus:ring-inset focus:ring-white"
                        >
                        <button type="submit" class="btn-on-dark whitespace-nowrap">Subscribe</button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">No spam. Unsubscribe anytime.</p>
                </form>
            </div>

            <!-- Company -->
            <nav class="lg:col-span-2" aria-label="Company">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Company</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="/about" class="hover:text-white">About Us</a></li>
                    <li><a href="/hse-policy" class="hover:text-white">HSE Policy</a></li>
                    <li><a href="/projects" class="hover:text-white">Projects</a></li>
                    <li><a href="/contact" class="hover:text-white">Contact</a></li>
                </ul>
            </nav>

            <!-- Services -->
            <nav class="lg:col-span-3" aria-label="Services">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Services</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="/services/engineering" class="hover:text-white">Engineering Services</a></li>
                    <li><a href="/services/energy-solutions" class="hover:text-white">Energy Solutions</a></li>
                    <li><a href="/services/procurement" class="hover:text-white">Procurement Services</a></li>
                    <li><a href="/services/hse-safety" class="hover:text-white">Safety / HSE Services</a></li>
                    <li><a href="/services/ict-solutions" class="hover:text-white">ICT Solutions</a></li>
                    <li><a href="/services/agro-food-processing" class="hover:text-white">Agro Products &amp; Food</a></li>
                </ul>
            </nav>

            <!-- Contact -->
            <div class="lg:col-span-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-white">Get in touch</h2>
                <address class="mt-4 space-y-3 text-sm not-italic">
                    <p class="flex items-start gap-2.5">
                        <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'location', 'class' => 'h-5 w-5']); ?></span>
                        <span><?php echo $this->escape($address); ?></span>
                    </p>
                    <p class="flex items-center gap-2.5">
                        <span class="text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'phone', 'class' => 'h-5 w-5']); ?></span>
                        <a href="tel:<?php echo $this->escape($phone); ?>" class="hover:text-white"><?php echo $this->escape($phoneDisplay); ?></a>
                    </p>
                    <p class="flex items-center gap-2.5">
                        <span class="text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'mail', 'class' => 'h-5 w-5']); ?></span>
                        <a href="mailto:<?php echo $this->escape($email); ?>" class="hover:text-white"><?php echo $this->escape($email); ?></a>
                    </p>
                </address>

                <ul class="mt-5 flex items-center gap-2">
                    <?php foreach ($socials as $social) : ?>
                        <li>
                            <a
                                href="<?php echo $this->escape($social['href']); ?>"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-white/5 text-gray-300 ring-1 ring-white/10 transition-colors hover:bg-white/10 hover:text-white"
                                target="_blank"
                                rel="noopener"
                            >
                                <span class="sr-only"><?php echo $this->escape($social['label']); ?></span>
                                <?php echo $this->partial('frontend/partials/icon', ['name' => $social['name'], 'class' => 'h-5 w-5']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-gray-400 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; <?php echo $year; ?> Desnky Global Resources Ltd. All rights reserved.</p>
            <nav aria-label="Legal" class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <a href="/privacy-policy" class="hover:text-white">Privacy Policy</a>
                <a href="/terms-of-use" class="hover:text-white">Terms of Use</a>
                <a href="/sitemap.xml" class="hover:text-white">Sitemap</a>
            </nav>
        </div>
    </div>
</footer>
