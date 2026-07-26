<?php
$hero = $hero ?? [];
$cart = $cart ?? [];
$items = $items ?? [];
$totals = $totals ?? ['subtotal' => 0, 'shipping' => 0, 'total' => 0];
$currency = (string) ($cart['currency_label'] ?? '₦');
$csrf = (string) ($csrf_token ?? '');
$heading = (string) ($hero['heading'] ?? $title ?? 'Your cart');
?>

<section class="section-band">
    <div class="container-page">
        <div class="mb-10 max-w-3xl" data-reveal>
            <h1 class="section-heading"><?php echo $this->escape($heading); ?></h1>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_22rem]">
            <?php if (empty($items)) : ?>
                <div class="rounded-lg border border-dashed border-gray-300 bg-desnky-surface p-10 text-center">
                    <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-white text-desnky-primary shadow-card"><?php echo $this->partial('frontend/partials/icon', ['name' => 'cart', 'class' => 'h-7 w-7']); ?></span>
                    <h2 class="mt-5 text-2xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($cart['empty_heading'] ?? 'Your cart is empty')); ?></h2>
                    <p class="mt-3 text-desnky-muted"><?php echo $this->escape((string) ($cart['empty_text'] ?? 'Browse our products and add items to your cart.')); ?></p>
                    <a href="/shop" class="btn-primary mt-6"><?php echo $this->escape((string) ($cart['continue_shopping_label'] ?? 'Continue shopping')); ?></a>
                </div>
            <?php else : ?>
                <form id="cart-update-form" action="/shop/cart/update" method="POST" class="space-y-4">
                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                    <?php foreach ($items as $item) : ?>
                        <div class="flex flex-col gap-4 rounded-lg border border-gray-200 bg-white p-4 shadow-card sm:flex-row sm:items-center">
                            <img src="<?php echo $this->escape((string) $item['image']); ?>" alt="<?php echo $this->escape((string) $item['name']); ?>" class="h-20 w-20 shrink-0 rounded-md object-cover" loading="lazy">
                            <div class="flex-1">
                                <p class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $item['name']); ?></p>
                                <p class="text-sm text-desnky-muted"><?php echo $this->escape((string) $item['sku']); ?></p>
                                <p class="mt-1 text-sm text-desnky-muted"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $item['price']); ?> each</p>
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="text-sm">
                                    <span class="sr-only">Quantity for <?php echo $this->escape((string) $item['name']); ?></span>
                                    <input type="number" min="0" max="<?php echo (int) $item['stock']; ?>" name="quantities[<?php echo $this->escape((string) $item['slug']); ?>]" value="<?php echo (int) $item['quantity']; ?>" class="form-field w-20" inputmode="numeric" data-cart-qty>
                                </label>
                                <div class="text-right">
                                    <p class="font-bold text-desnky-dark"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $item['subtotal']); ?></p>
                                    <button type="button" class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-red-700 hover:text-red-800" data-cart-remove aria-label="Remove <?php echo $this->escape((string) $item['name']); ?> from cart">
                                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'trash', 'class' => 'h-4 w-4']); ?>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="btn-secondary"><?php echo $this->partial('frontend/partials/icon', ['name' => 'check', 'class' => 'h-5 w-5']); ?> <?php echo $this->escape((string) ($cart['update_cart_label'] ?? 'Update cart')); ?></button>
                        <a href="/shop" class="btn-ghost self-center"><?php echo $this->escape((string) ($cart['continue_shopping_label'] ?? 'Continue shopping')); ?></a>
                    </div>
                </form>
            <?php endif; ?>

            <aside class="h-fit rounded-lg border border-gray-200 bg-desnky-surface p-6 lg:sticky lg:top-28">
                <h2 class="text-xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($cart['summary_heading'] ?? 'Order summary')); ?></h2>
                <dl class="mt-5 space-y-3 text-sm text-desnky-ink">
                    <div class="flex justify-between"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($cart['subtotal_label'] ?? 'Subtotal')); ?></dt><dd class="font-semibold"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $totals['subtotal']); ?></dd></div>
                    <div class="flex justify-between"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($cart['shipping_label'] ?? 'Delivery')); ?></dt><dd class="font-semibold"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $totals['shipping']); ?></dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold text-desnky-dark"><dt><?php echo $this->escape((string) ($cart['total_label'] ?? 'Total')); ?></dt><dd><?php echo $this->escape($currency); ?> <?php echo number_format((float) $totals['total']); ?></dd></div>
                </dl>
                <a href="/shop/checkout" class="btn-primary mt-6 w-full <?php echo empty($items) ? 'pointer-events-none opacity-50' : ''; ?>"><?php echo $this->escape((string) ($cart['checkout_label'] ?? 'Proceed to checkout')); ?></a>
                <p class="mt-3 text-center text-xs text-desnky-muted">Pay on delivery or by bank transfer.</p>
            </aside>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('cart-update-form');

    submitForm('#cart-update-form', {
        endpoint: '/shop/cart/update',
        resetOnSuccess: false,
        onSuccess: function () { window.location.reload(); }
    });

    // Remove = set this line's quantity to 0, then submit the update form.
    if (form) {
        form.querySelectorAll('[data-cart-remove]').forEach(function (button) {
            button.addEventListener('click', function () {
                var row = button.closest('.rounded-lg');
                var qty = row && row.querySelector('[data-cart-qty]');
                if (qty) {
                    qty.value = '0';
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.dispatchEvent(new Event('submit', { cancelable: true }));
                    }
                }
            });
        });
    }
});
</script>
