<?php

$form = \App\Helpers\FormHelper::class;
?>

<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-desnky-blue">Contact</p>
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-desnky-navy sm:text-5xl">
                <?php echo $this->escape($title); ?>
            </h1>
            <p class="mt-5 text-lg leading-8 text-desnky-muted">
                Send us your project, procurement, safety, ICT or agro enquiry. Our team will respond with the next
                practical step.
            </p>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-10 lg:grid-cols-[minmax(0,1fr)_24rem]">
        <form id="contact-form" action="/contact/submit" method="POST" class="relative grid gap-6">
            <?php echo $form::token($csrf_token ?? null); ?>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="full_name" class="form-label">Full Name *</label>
                    <?php echo $form::textInput('full_name', '', 'Your full name', ['required' => true]); ?>
                </div>

                <div>
                    <label for="email" class="form-label">Email Address *</label>
                    <?php echo $form::emailInput('email', '', 'you@example.com', ['required' => true]); ?>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="phone" class="form-label">Phone Number *</label>
                    <?php echo $form::textInput('phone', '', '08012345678', ['required' => true]); ?>
                </div>

                <div>
                    <label for="company" class="form-label">Company</label>
                    <?php echo $form::textInput('company', '', 'Company name'); ?>
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="service_interested" class="form-label">Service Interest</label>
                    <?php echo $form::select('service_interested', [
                        '' => 'Select a service',
                        'engineering' => 'Engineering Services',
                        'energy' => 'Energy Solutions',
                        'procurement' => 'Procurement Services',
                        'hse' => 'HSE and Safety',
                        'ict' => 'ICT Solutions',
                        'agro' => 'Agro and Food Processing',
                    ]); ?>
                </div>

                <div>
                    <label for="subject" class="form-label">Subject *</label>
                    <?php echo $form::textInput('subject', '', 'How can we help?', ['required' => true]); ?>
                </div>
            </div>

            <div>
                <label for="message" class="form-label">Message *</label>
                <?php echo $form::textarea('message', '', 6, ['required' => true]); ?>
            </div>

            <div>
                <?php echo $form::submit('Send Message', ['class' => 'btn-primary w-full sm:w-auto']); ?>
            </div>
        </form>

        <aside class="bg-desnky-navy p-8 text-white">
            <h2 class="text-xl font-bold">Company Information</h2>
            <div class="mt-6 space-y-5 text-sm leading-6 text-gray-200">
                <p>
                    Desnky Global Resources Ltd supports organizations across energy, engineering, procurement,
                    safety, ICT and agro sectors.
                </p>
                <p><strong class="text-white">Email:</strong><br>
                    <a href="mailto:info@desnkygroup.com" class="hover:text-desnky-gold">info@desnkygroup.com</a>
                </p>
                <p><strong class="text-white">Phone:</strong><br>
                    <a href="tel:+2340000000000" class="hover:text-desnky-gold">+234 000 000 0000</a>
                </p>
                <p><strong class="text-white">Location:</strong><br>Lagos, Nigeria</p>
                <p><strong class="text-white">Business Hours:</strong><br>Monday to Friday, 9:00 AM - 5:00 PM</p>
            </div>
        </aside>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('#contact-form', {
        endpoint: '/contact/submit',
        resetOnSuccess: true
    });
});
</script>
