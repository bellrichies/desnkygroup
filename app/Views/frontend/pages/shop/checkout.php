<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h1 class="text-4xl font-bold text-desnky-navy">Checkout</h1>
        <p class="mt-4 text-desnky-muted">Enter delivery details and choose your payment method.</p>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_24rem]">
        <form id="checkout-form" action="/shop/checkout" method="POST" class="grid gap-6">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input id="full_name" name="full_name" required class="form-field" autocomplete="name">
                </div>
                <div>
                    <label for="email" class="form-label">Email *</label>
                    <input id="email" name="email" type="email" required class="form-field" autocomplete="email">
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="phone" class="form-label">Phone *</label>
                    <input id="phone" name="phone" required class="form-field" autocomplete="tel">
                </div>
                <div>
                    <label for="city_state" class="form-label">City / State *</label>
                    <input id="city_state" name="city_state" required class="form-field">
                </div>
            </div>

            <div>
                <label for="delivery_address" class="form-label">Delivery Address *</label>
                <textarea id="delivery_address" name="delivery_address" required rows="4" class="form-field"></textarea>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="payment_method" class="form-label">Payment Method *</label>
                    <select id="payment_method" name="payment_method" required class="form-field">
                        <option value="">Choose payment method</option>
                        <option value="bank_transfer">Manual bank transfer</option>
                        <option value="pay_on_delivery">Pay on delivery</option>
                    </select>
                </div>
                <div>
                    <label for="order_notes" class="form-label">Order Notes</label>
                    <input id="order_notes" name="order_notes" class="form-field">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full sm:w-auto" <?php echo empty($items) ? 'disabled' : ''; ?>>
                Confirm Order
            </button>
        </form>

        <aside class="h-fit bg-desnky-surface p-6">
            <h2 class="text-xl font-bold text-desnky-navy">Order Review</h2>
            <?php if (empty($items)) : ?>
                <p class="mt-4 text-sm text-desnky-muted">Your cart is empty.</p>
                <a href="/shop" class="btn-primary mt-6 w-full">Return to Shop</a>
            <?php else : ?>
                <ul class="mt-5 space-y-4">
                    <?php foreach ($items as $item) : ?>
                        <li class="flex justify-between gap-4 text-sm">
                            <span><?php echo (int) $item['quantity']; ?> x <?php echo $this->escape($item['name']); ?></span>
                            <span class="font-semibold">NGN <?php echo number_format($item['subtotal']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <dl class="mt-5 space-y-3 border-t border-gray-200 pt-5 text-sm">
                    <div class="flex justify-between"><dt>Subtotal</dt><dd>NGN <?php echo number_format($totals['subtotal']); ?></dd></div>
                    <div class="flex justify-between"><dt>Shipping estimate</dt><dd>NGN <?php echo number_format($totals['shipping']); ?></dd></div>
                    <div class="flex justify-between text-base font-bold text-desnky-navy"><dt>Total</dt><dd>NGN <?php echo number_format($totals['total']); ?></dd></div>
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
