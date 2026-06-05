<?php
$form = \App\Helpers\FormHelper::class;

// Pre-fill the service interest from a referring service page (?service=hse etc.).
$serviceOptions = [
    '' => 'Select a service',
    'engineering' => 'Engineering Services',
    'energy' => 'Energy Solutions',
    'procurement' => 'Procurement Services',
    'hse' => 'HSE and Safety',
    'ict' => 'ICT Solutions',
    'agro' => 'Agro and Food Processing',
];
$selectedService = (string) ($_GET['service'] ?? '');
if (!isset($serviceOptions[$selectedService])) {
    $selectedService = '';
}

$site = $site ?? [];
$phone = (string) ($site['phone'] ?? '+2340000000000');
$phoneDisplay = (string) ($site['phone_display'] ?? $phone);
$whatsapp = (string) ($site['whatsapp'] ?? '2340000000000');
$email = (string) ($site['email'] ?? 'info@desnkygroup.com');
$address = (string) ($site['address'] ?? 'Lagos, Nigeria');
$hours = (string) ($site['hours'] ?? 'Mon–Fri, 9:00 AM – 5:00 PM');

$hero = [
    'eyebrow' => 'Contact',
    'heading' => $title ?? "Let's discuss your project",
    'text' => 'Send us your project, procurement, safety, ICT or agro enquiry. Our team responds within one business day with a clear next step.',
    'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1800&q=80',
    'image_alt' => 'Business team reviewing an enquiry in a meeting',
];

$faqs = [
    ['q' => 'How quickly will I get a response?', 'a' => 'We aim to respond to every enquiry within one business day. For urgent matters, call or message us on WhatsApp for the fastest reply.'],
    ['q' => 'Do you handle projects outside Lagos?', 'a' => 'Yes. We deliver engineering, energy, procurement, HSE and ICT projects nationwide across Nigeria, mobilising teams and equipment to your site.'],
    ['q' => 'Can I request bulk or B2B pricing?', 'a' => 'Absolutely. Tell us your requirements in the form and select the relevant service, and our team will prepare a tailored quotation.'],
];
?>

<?php echo $this->partial('frontend/partials/page-hero', [
    'hero' => $hero,
    'title' => $title ?? '',
    'breadcrumbs' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Contact'],
    ],
]); ?>

<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <!-- Form -->
        <div data-reveal>
            <h2 class="text-2xl font-bold text-desnky-dark">Send us a message</h2>
            <p class="mt-2 text-desnky-muted">Fields marked with * are required.</p>

            <form id="contact-form" action="/contact/submit" method="POST" class="relative mt-8 grid gap-6">
                <?php echo $form::token($csrf_token ?? null); ?>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="full_name" class="form-label">Full Name *</label>
                        <?php echo $form::textInput('full_name', '', 'Your full name', ['required' => true, 'autocomplete' => 'name']); ?>
                    </div>
                    <div>
                        <label for="email" class="form-label">Email Address *</label>
                        <?php echo $form::emailInput('email', '', 'you@example.com', ['required' => true, 'autocomplete' => 'email']); ?>
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="form-label">Phone Number *</label>
                        <?php echo $form::textInput('phone', '', '08012345678', ['required' => true, 'autocomplete' => 'tel', 'inputmode' => 'tel']); ?>
                    </div>
                    <div>
                        <label for="company" class="form-label">Company *</label>
                        <?php echo $form::textInput('company', '', 'Company name', ['required' => true, 'autocomplete' => 'organization']); ?>
                    </div>
                </div>

                <div>
                    <label for="service_interested" class="form-label">Service Interest *</label>
                    <?php echo $form::select('service_interested', $serviceOptions, $selectedService, ['required' => true]); ?>
                </div>

                <div>
                    <label for="message" class="form-label">Message *</label>
                    <?php echo $form::textarea('message', '', 6, ['required' => true, 'placeholder' => 'Tell us about your project, timeline and requirements…']); ?>
                </div>

                <div class="rounded-md bg-desnky-surface p-4">
                    <?php echo $form::checkbox('consent', '1', false, 'I consent to Desnky Global Resources contacting me about this enquiry. *', ['required' => true]); ?>
                </div>

                <div>
                    <?php echo $form::submit('Send Message', ['class' => 'btn-primary w-full sm:w-auto']); ?>
                </div>
            </form>
        </div>

        <!-- Contact details -->
        <aside class="space-y-6" data-reveal>
            <div class="rounded-lg bg-desnky-dark p-7 text-white">
                <h2 class="text-lg font-bold">Talk to us directly</h2>
                <ul class="mt-5 space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'phone', 'class' => 'h-5 w-5']); ?></span>
                        <span><span class="block font-semibold text-white">Phone</span><a href="tel:<?php echo $this->escape($phone); ?>" class="text-gray-200 hover:text-white"><?php echo $this->escape($phoneDisplay); ?></a></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'mail', 'class' => 'h-5 w-5']); ?></span>
                        <span><span class="block font-semibold text-white">Email</span><a href="mailto:<?php echo $this->escape($email); ?>" class="text-gray-200 hover:text-white"><?php echo $this->escape($email); ?></a></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'location', 'class' => 'h-5 w-5']); ?></span>
                        <span><span class="block font-semibold text-white">Office</span><span class="text-gray-200"><?php echo $this->escape($address); ?></span></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'clock', 'class' => 'h-5 w-5']); ?></span>
                        <span><span class="block font-semibold text-white">Hours</span><span class="text-gray-200"><?php echo $this->escape($hours); ?></span></span>
                    </li>
                </ul>
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="tel:<?php echo $this->escape($phone); ?>" class="btn-on-dark">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'phone', 'class' => 'h-5 w-5']); ?>
                        Call
                    </a>
                    <a href="https://wa.me/<?php echo $this->escape($whatsapp); ?>" class="btn-secondary-on-dark" target="_blank" rel="noopener">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'whatsapp', 'class' => 'h-5 w-5']); ?>
                        WhatsApp
                    </a>
                </div>
            </div>

            <!-- Map facade (loads embed only on interaction — CWV friendly) -->
            <div
                x-data="{ loaded: false }"
                class="relative aspect-[4/3] overflow-hidden rounded-lg border border-gray-200 bg-desnky-surface"
            >
                <template x-if="!loaded">
                    <button
                        type="button"
                        @click="loaded = true"
                        class="group absolute inset-0 flex flex-col items-center justify-center gap-2 text-desnky-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-desnky-primary"
                        aria-label="Load map of our Lagos office"
                    >
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'location', 'class' => 'h-8 w-8']); ?>
                        <span class="text-sm font-semibold">View our Lagos office on the map</span>
                    </button>
                </template>
                <template x-if="loaded">
                    <iframe
                        title="Map of Desnky Global Resources, Lagos"
                        src="https://www.google.com/maps?q=Lagos,Nigeria&output=embed"
                        class="absolute inset-0 h-full w-full"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </template>
            </div>
        </aside>
    </div>
</section>

<!-- FAQ -->
<section class="section-band bg-desnky-surface">
    <div class="container-page max-w-3xl">
        <div class="text-center" data-reveal>
            <p class="eyebrow">Before you ask</p>
            <h2 class="mt-3 section-heading">Frequently asked questions</h2>
        </div>
        <div class="mt-10 divide-y divide-gray-200 rounded-lg border border-gray-200 bg-white" x-data="{ open: 0 }">
            <?php foreach ($faqs as $i => $faq) : ?>
                <div>
                    <h3>
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left font-semibold text-desnky-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-desnky-primary"
                            @click="open = open === <?php echo $i; ?> ? null : <?php echo $i; ?>"
                            :aria-expanded="(open === <?php echo $i; ?>).toString()"
                        >
                            <?php echo $this->escape($faq['q']); ?>
                            <span :class="open === <?php echo $i; ?> && 'rotate-180'" class="shrink-0 text-desnky-primary transition-transform">
                                <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-5 w-5']); ?>
                            </span>
                        </button>
                    </h3>
                    <div x-show="open === <?php echo $i; ?>" x-collapse x-cloak>
                        <p class="px-6 pb-5 text-sm leading-7 text-desnky-muted"><?php echo $this->escape($faq['a']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script type="application/ld+json">
<?php echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static fn (array $faq): array => [
        '@type' => 'Question',
        'name' => $faq['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
    ], $faqs),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('#contact-form', { endpoint: '/contact/submit', resetOnSuccess: true });
});
</script>
