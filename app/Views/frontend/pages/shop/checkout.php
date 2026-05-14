<?php
$hero = $hero ?? [];
$checkout = $checkout ?? [];
$items = $items ?? [];
$totals = $totals ?? ['subtotal' => 0, 'shipping' => 0, 'total' => 0];
$paymentMethods = is_array($checkout['payment_methods'] ?? null) ? $checkout['payment_methods'] : [];
?>

<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h1 class="text-4xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($hero['heading'] ?? $title)); ?></h1>
        <?php if (!empty($hero['text'])) : ?>
            <p class="mt-4 text-desnky-muted"><?php echo $this->escape((string) $hero['text']); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_24rem]">
        <form id="checkout-form" action="/shop/checkout" method="POST" class="grid gap-6">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="full_name" class="form-label"><?php echo $this->escape((string) ($checkout['full_name_label'] ?? '')); ?></label>
                    <input id="full_name" name="full_name" required class="form-field" autocomplete="name">
                </div>
                <div>
                    <label for="email" class="form-label"><?php echo $this->escape((string) ($checkout['email_label'] ?? '')); ?></label>
                    <input id="email" name="email" type="email" required class="form-field" autocomplete="email">
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="phone" class="form-label"><?php echo $this->escape((string) ($checkout['phone_label'] ?? '')); ?></label>
                    <input id="phone" name="phone" required class="form-field" autocomplete="tel">
                </div>
                <div>
                    <label for="city_state" class="form-label"><?php echo $this->escape((string) ($checkout['city_state_label'] ?? '')); ?></label>
                    <input id="city_state" name="city_state" required class="form-field">
                </div>
            </div>

            <div>
                <label for="delivery_address" class="form-label"><?php echo $this->escape((string) ($checkout['delivery_address_label'] ?? '')); ?></label>
                <textarea id="delivery_address" name="delivery_address" required rows="4" class="form-field"></textarea>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="payment_method" class="form-label"><?php echo $this->escape((string) ($checkout['payment_method_label'] ?? '')); ?></label>
                    <select id="payment_method" name="payment_method" required class="form-field">
                        <option value=""><?php echo $this->escape((string) ($checkout['payment_method_placeholder'] ?? '')); ?></option>
                        <?php foreach ($paymentMethods as $method) : ?>
                            <option value="<?php echo $this->escape((string) ($method['value'] ?? '')); ?>"><?php echo $this->escape((string) ($method['label'] ?? '')); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="order_notes" class="form-label"><?php echo $this->escape((string) ($checkout['order_notes_label'] ?? '')); ?></label>
                    <input id="order_notes" name="order_notes" class="form-field">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto" <?php echo empty($items) ? 'disabled' : ''; ?>>
                <?php echo $this->escape((string) ($checkout['submit_label'] ?? '')); ?>
            </button>
        </form>

        <aside class="h-fit bg-desnky-surface p-6">
            <h2 class="text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($checkout['review_heading'] ?? '')); ?></h2>
            <?php if (empty($items)) : ?>
                <p class="mt-4 text-sm text-desnky-muted"><?php echo $this->escape((string) ($checkout['empty_text'] ?? '')); ?></p>
                <a href="/shop" class="btn-primary mt-6 w-full"><?php echo $this->escape((string) ($checkout['return_to_shop_label'] ?? '')); ?></a>
            <?php else : ?>
                <ul class="mt-5 space-y-4">
                    <?php foreach ($items as $item) : ?>
                        <li class="flex justify-between gap-4 text-sm">
                            <span><?php echo (int) $item['quantity']; ?> <?php echo $this->escape((string) ($checkout['quantity_separator'] ?? '')); ?> <?php echo $this->escape((string) $item['name']); ?></span>
                            <span class="font-semibold"><?php echo $this->escape((string) ($checkout['currency_label'] ?? '')); ?> <?php echo number_format((float) $item['subtotal']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <dl class="mt-5 space-y-3 border-t border-gray-200 pt-5 text-sm">
                    <div class="flex justify-between"><dt><?php echo $this->escape((string) ($checkout['subtotal_label'] ?? '')); ?></dt><dd><?php echo $this->escape((string) ($checkout['currency_label'] ?? '')); ?> <?php echo number_format((float) $totals['subtotal']); ?></dd></div>
                    <div class="flex justify-between"><dt><?php echo $this->escape((string) ($checkout['shipping_label'] ?? '')); ?></dt><dd><?php echo $this->escape((string) ($checkout['currency_label'] ?? '')); ?> <?php echo number_format((float) $totals['shipping']); ?></dd></div>
                    <div class="flex justify-between text-base font-bold text-desnky-navy"><dt><?php echo $this->escape((string) ($checkout['total_label'] ?? '')); ?></dt><dd><?php echo $this->escape((string) ($checkout['currency_label'] ?? '')); ?> <?php echo number_format((float) $totals['total']); ?></dd></div>
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
            window.setTimeout(function () {
                window.location.href = '/shop';
            }, 1200);
        }
    });
});
</script>
