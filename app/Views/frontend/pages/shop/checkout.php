<?php
$hero = $hero ?? [];
$checkout = $checkout ?? [];
$items = $items ?? [];
$totals = $totals ?? ['subtotal' => 0, 'shipping' => 0, 'total' => 0];
$paymentMethods = is_array($checkout['payment_methods'] ?? null) ? $checkout['payment_methods'] : [];
$currency = (string) ($checkout['currency_label'] ?? '₦');
$csrf = (string) ($csrf_token ?? '');
?>

<?php echo $this->partial('frontend/partials/page-hero', [
    'hero' => [
        'eyebrow' => 'Shop',
        'heading' => (string) ($hero['heading'] ?? $title ?? 'Checkout'),
        'text' => (string) ($hero['text'] ?? 'Complete your order — it only takes a minute.'),
    ],
    'breadcrumbs' => [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Shop', 'href' => '/shop'],
        ['label' => 'Cart', 'href' => '/shop/cart'],
        ['label' => 'Checkout'],
    ],
]); ?>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_24rem]">
        <form id="checkout-form" action="/shop/checkout" method="POST" class="space-y-8" x-data="{ payment: '' }">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">

            <fieldset class="rounded-lg border border-gray-200 bg-white p-6 shadow-card">
                <legend class="px-2 text-lg font-bold text-desnky-dark">Your details</legend>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="full_name" class="form-label"><?php echo $this->escape((string) ($checkout['full_name_label'] ?? 'Full name')); ?></label>
                        <input id="full_name" name="full_name" required class="form-field" autocomplete="name">
                    </div>
                    <div>
                        <label for="email" class="form-label"><?php echo $this->escape((string) ($checkout['email_label'] ?? 'Email')); ?></label>
                        <input id="email" name="email" type="email" required class="form-field" autocomplete="email" inputmode="email">
                    </div>
                    <div>
                        <label for="phone" class="form-label"><?php echo $this->escape((string) ($checkout['phone_label'] ?? 'Phone')); ?></label>
                        <input id="phone" name="phone" required class="form-field" autocomplete="tel" inputmode="tel">
                    </div>
                    <div>
                        <label for="city_state" class="form-label"><?php echo $this->escape((string) ($checkout['city_state_label'] ?? 'City / State')); ?></label>
                        <input id="city_state" name="city_state" required class="form-field" autocomplete="address-level2">
                    </div>
                </div>
            </fieldset>

            <fieldset class="rounded-lg border border-gray-200 bg-white p-6 shadow-card">
                <legend class="px-2 text-lg font-bold text-desnky-dark">Delivery</legend>
                <div>
                    <label for="delivery_address" class="form-label"><?php echo $this->escape((string) ($checkout['delivery_address_label'] ?? 'Delivery address')); ?></label>
                    <textarea id="delivery_address" name="delivery_address" required rows="3" class="form-field" autocomplete="street-address"></textarea>
                </div>
                <div class="mt-6">
                    <label for="order_notes" class="form-label"><?php echo $this->escape((string) ($checkout['order_notes_label'] ?? 'Order notes (optional)')); ?></label>
                    <input id="order_notes" name="order_notes" class="form-field">
                </div>
            </fieldset>

            <fieldset class="rounded-lg border border-gray-200 bg-white p-6 shadow-card">
                <legend class="px-2 text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) ($checkout['payment_method_label'] ?? 'Payment method')); ?></legend>
                <div class="grid gap-3">
                    <?php foreach ($paymentMethods as $method) : ?>
                        <?php $value = (string) ($method['value'] ?? ''); ?>
                        <label class="flex cursor-pointer items-start gap-3 rounded-md border p-4 transition-colors" :class="payment === <?php echo $this->escapeJson($value); ?> ? 'border-desnky-primary bg-desnky-primary-50' : 'border-gray-300 hover:border-desnky-primary'">
                            <input type="radio" name="payment_method" value="<?php echo $this->escape($value); ?>" x-model="payment" required class="mt-0.5 text-desnky-primary focus:ring-desnky-primary">
                            <span>
                                <span class="block font-semibold text-desnky-dark"><?php echo $this->escape((string) ($method['label'] ?? '')); ?></span>
                                <?php if (!empty($method['description'])) : ?>
                                    <span class="mt-0.5 block text-sm text-desnky-muted"><?php echo $this->escape((string) $method['description']); ?></span>
                                <?php endif; ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div x-show="payment === 'bank_transfer' || payment === 'transfer'" x-collapse x-cloak class="mt-4">
                    <div class="alert-info">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'document', 'class' => 'h-5 w-5 shrink-0']); ?>
                        <p>Bank transfer details will be emailed with your order confirmation. Your order is reserved once payment is received.</p>
                    </div>
                </div>
            </fieldset>

            <button type="submit" class="btn-primary w-full sm:w-auto" <?php echo empty($items) ? 'disabled' : ''; ?>>
                <?php echo $this->escape((string) ($checkout['submit_label'] ?? 'Place order')); ?>
            </button>
        </form>

        <aside class="h-fit rounded-lg border border-gray-200 bg-desnky-surface p-6 lg:sticky lg:top-28">
            <h2 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($checkout['review_heading'] ?? 'Order review')); ?></h2>
            <?php if (empty($items)) : ?>
                <p class="mt-4 text-sm text-desnky-muted"><?php echo $this->escape((string) ($checkout['empty_text'] ?? 'Your cart is empty.')); ?></p>
                <a href="/shop" class="btn-primary mt-6 w-full"><?php echo $this->escape((string) ($checkout['return_to_shop_label'] ?? 'Return to shop')); ?></a>
            <?php else : ?>
                <ul class="mt-5 space-y-4">
                    <?php foreach ($items as $item) : ?>
                        <li class="flex justify-between gap-4 text-sm">
                            <span class="text-desnky-ink"><span class="font-semibold"><?php echo (int) $item['quantity']; ?>×</span> <?php echo $this->escape((string) $item['name']); ?></span>
                            <span class="shrink-0 font-semibold text-desnky-dark"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $item['subtotal']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <dl class="mt-5 space-y-3 border-t border-gray-200 pt-5 text-sm">
                    <div class="flex justify-between"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($checkout['subtotal_label'] ?? 'Subtotal')); ?></dt><dd class="font-semibold"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $totals['subtotal']); ?></dd></div>
                    <div class="flex justify-between"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($checkout['shipping_label'] ?? 'Delivery')); ?></dt><dd class="font-semibold"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $totals['shipping']); ?></dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold text-desnky-dark"><dt><?php echo $this->escape((string) ($checkout['total_label'] ?? 'Total')); ?></dt><dd><?php echo $this->escape($currency); ?> <?php echo number_format((float) $totals['total']); ?></dd></div>
                </dl>
            <?php endif; ?>
        </aside>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('#checkout-form', {
        endpoint: '/shop/checkout',
        resetOnSuccess: true,
        onSuccess: function (response) {
            var message = response.order_number ? ' Order number: ' + response.order_number : '';
            createToastNotification((response.message || 'Order received.') + message, 'success', 6000);
            window.setTimeout(function () { window.location.href = '/shop'; }, 1500);
        }
    });
});
</script>
