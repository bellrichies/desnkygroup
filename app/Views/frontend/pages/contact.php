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

$site ??= [];
$phone = (string) ($site['phone'] ?? '+2340000000000');
$phoneDisplay = (string) ($site['phone_display'] ?? $phone);
$whatsapp = (string) ($site['whatsapp'] ?? '2340000000000');
$email = (string) ($site['email'] ?? 'info@desnkygroup.com');
$address = (string) ($site['address'] ?? 'Lagos, Nigeria');
$hours = (string) ($site['hours'] ?? 'Mon-Fri, 9:00 AM - 5:00 PM');
$mapQuery = rawurlencode($address);
$mapTitle = 'Map of ' . $address;

$faqs ??= [];
?>

<section class="section-band bg-white">
    <div class="container-page">
        <div class="max-w-3xl" data-reveal>
            <p class="eyebrow">Contact</p>
            <h1 class="mt-3 section-heading"><?php echo $this->escape((string) ($title ?? "Let's discuss your project")); ?></h1>
            <p class="section-lead">Send your project, procurement, safety, ICT or agro enquiry. Our team responds with a clear next step.</p>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_23rem] lg:items-start">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-card sm:p-8" data-reveal>
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div>
                        <h2 class="text-2xl font-bold text-desnky-dark">Send us a message</h2>
                        <p class="mt-2 text-sm text-desnky-muted">Fields marked with * are required.</p>
                    </div>
                    <span class="badge-brand self-start">Secure form</span>
                </div>

                <div id="contact-success" hidden class="mt-6 rounded-lg border border-green-200 bg-green-50 px-6 py-5" role="status">
                    <h3 class="font-bold text-green-800">Message received.</h3>
                    <p class="mt-1 text-sm text-green-700">Thank you for reaching out. Our team will get back to you within one business day.</p>
                </div>

                <div id="contact-error" hidden class="mt-6 rounded-lg border border-red-200 bg-red-50 px-6 py-5" role="alert">
                    <h3 class="font-bold text-red-800">Message not sent.</h3>
                    <p class="mt-1 text-sm text-red-700" data-contact-error-message>Please review the form and try again.</p>
                </div>

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
                        <?php echo $form::textarea('message', '', 6, ['required' => true, 'placeholder' => 'Tell us about your project, timeline and requirements']); ?>
                    </div>

                    <div class="rounded-md bg-desnky-surface p-4">
                        <?php echo $form::checkbox('consent', '1', false, 'I consent to Desnky Global Resources contacting me about this enquiry. *', ['required' => true]); ?>
                    </div>

                    <div>
                        <?php echo $form::submit('Send Message', ['class' => 'btn-primary w-full sm:w-auto']); ?>
                    </div>
                </form>
            </div>

            <aside class="space-y-5 lg:sticky lg:top-28" data-reveal>
                <div class="rounded-lg bg-desnky-dark p-6 text-white shadow-card sm:p-7">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-lg font-bold">Talk to us directly</h2>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-desnky-primary-200">Office</span>
                    </div>
                    <ul class="mt-5 space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'phone', 'class' => 'h-5 w-5']); ?></span>
                            <span>
                                <span class="block font-semibold text-white">Phone</span>
                                <a href="tel:<?php echo $this->escape($phone); ?>" class="text-gray-200 hover:text-white"><?php echo $this->escape($phoneDisplay); ?></a>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'mail', 'class' => 'h-5 w-5']); ?></span>
                            <span>
                                <span class="block font-semibold text-white">Email</span>
                                <a href="mailto:<?php echo $this->escape($email); ?>" class="text-gray-200 hover:text-white"><?php echo $this->escape($email); ?></a>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'location', 'class' => 'h-5 w-5']); ?></span>
                            <span>
                                <span class="block font-semibold text-white">Office</span>
                                <span class="text-gray-200"><?php echo $this->escape($address); ?></span>
                            </span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 text-desnky-primary-200"><?php echo $this->partial('frontend/partials/icon', ['name' => 'clock', 'class' => 'h-5 w-5']); ?></span>
                            <span>
                                <span class="block font-semibold text-white">Hours</span>
                                <span class="text-gray-200"><?php echo $this->escape($hours); ?></span>
                            </span>
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

                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-bold uppercase tracking-wide text-desnky-primary">What happens next</p>
                    <p class="mt-2 text-sm leading-6 text-desnky-muted">We review your request, confirm scope and urgency, then follow up with the right service lead within one business day.</p>
                </div>

                <div
                    x-data="{ loaded: false }"
                    class="relative aspect-[4/3] overflow-hidden rounded-lg border border-gray-200 bg-desnky-surface shadow-sm"
                >
                    <template x-if="!loaded">
                        <button
                            type="button"
                            @click="loaded = true"
                            class="absolute inset-0 flex flex-col items-center justify-center gap-2 px-6 text-center text-desnky-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-desnky-primary"
                            aria-label="<?php echo $this->escape($mapTitle); ?>"
                        >
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'location', 'class' => 'h-8 w-8']); ?>
                            <span class="text-sm font-semibold">View our office on the map</span>
                            <span class="text-xs text-desnky-muted"><?php echo $this->escape($address); ?></span>
                        </button>
                    </template>
                    <template x-if="loaded">
                        <iframe
                            title="<?php echo $this->escape($mapTitle); ?>"
                            src="https://www.google.com/maps?q=<?php echo $this->escape($mapQuery); ?>&amp;output=embed"
                            class="absolute inset-0 h-full w-full"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </template>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section-band bg-desnky-surface">
    <div class="container-page max-w-3xl">
        <div class="text-center" data-reveal>
            <p class="eyebrow">Before you ask</p>
            <h2 class="mt-3 section-heading">Frequently asked questions</h2>
        </div>
        <?php if (!empty($faqs)) : ?>
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
                            <?php echo $this->escape((string) ($faq['question'] ?? '')); ?>
                            <span :class="open === <?php echo $i; ?> && 'rotate-180'" class="shrink-0 text-desnky-primary transition-transform">
                                <?php echo $this->partial('frontend/partials/icon', ['name' => 'chevron-down', 'class' => 'h-5 w-5']); ?>
                            </span>
                        </button>
                    </h3>
                    <div x-show="open === <?php echo $i; ?>" x-collapse x-cloak>
                        <p class="px-6 pb-5 text-sm leading-7 text-desnky-muted"><?php echo $this->escape((string) ($faq['answer'] ?? '')); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <p class="mt-10 text-center text-sm text-desnky-muted">Have a question? Use the contact form above and our team will be happy to help.</p>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($faqs)) : ?>
<script type="application/ld+json">
<?php echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(static fn (array $faq): array => [
        '@type' => 'Question',
        'name' => (string) ($faq['question'] ?? ''),
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => (string) ($faq['answer'] ?? '')],
    ], $faqs),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('#contact-form', {
        endpoint: '/contact/submit',
        resetOnSuccess: true,
        onSuccess: function () {
            var form = document.getElementById('contact-form');
            var success = document.getElementById('contact-success');
            var error = document.getElementById('contact-error');
            if (error) { error.hidden = true; }
            if (form) { form.hidden = true; }
            if (success) { success.hidden = false; }
        },
        onError: function (payload) {
            var error = document.getElementById('contact-error');
            var message = document.querySelector('[data-contact-error-message]');
            if (message && payload && payload.message) {
                message.textContent = payload.message;
            }
            if (error) { error.hidden = false; }
        },
    });
});
</script>
