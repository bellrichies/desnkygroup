<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h1 class="text-4xl font-bold text-desnky-navy">Shopping Cart</h1>
        <p class="mt-4 text-desnky-muted">Review your selected products before checkout.</p>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_22rem]">
        <?php if (empty($items)) : ?>
            <div class="bg-desnky-surface p-8">
                <h2 class="text-2xl font-bold text-desnky-navy">Your cart is empty</h2>
                <p class="mt-3 text-desnky-muted">Browse the shop to add safety, ICT, industrial or agro products.</p>
                <a href="/shop" class="btn-primary mt-6">Continue Shopping</a>
            </div>
        <?php else : ?>
            <form id="cart-update-form" action="/shop/cart/update" method="POST" class="overflow-x-auto">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-desnky-surface text-left text-desnky-navy">
                        <tr>
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-4">
                                        <img src="<?php echo $this->escape($item['image']); ?>" alt="<?php echo $this->escape($item['name']); ?>" class="h-16 w-16 object-cover" loading="lazy">
                                        <div>
                                            <p class="font-semibold text-desnky-navy"><?php echo $this->escape($item['name']); ?></p>
                                            <p class="text-desnky-muted"><?php echo $this->escape($item['sku']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">NGN <?php echo number_format($item['price']); ?></td>
                                <td class="px-4 py-4">
                                    <input type="number" min="0" max="<?php echo (int) $item['stock']; ?>" name="quantities[<?php echo $this->escape($item['slug']); ?>]" value="<?php echo (int) $item['quantity']; ?>" class="form-field w-24">
                                </td>
                                <td class="px-4 py-4 font-semibold text-desnky-navy">NGN <?php echo number_format($item['subtotal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="btn-secondary">Update Cart</button>
                    <a href="/shop" class="btn-secondary">Continue Shopping</a>
                </div>
            </form>
        <?php endif; ?>

        <aside class="h-fit bg-desnky-surface p-6">
            <h2 class="text-xl font-bold text-desnky-navy">Cart Summary</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between"><dt>Subtotal</dt><dd>NGN <?php echo number_format($totals['subtotal']); ?></dd></div>
                <div class="flex justify-between"><dt>Shipping estimate</dt><dd>NGN <?php echo number_format($totals['shipping']); ?></dd></div>
                <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold text-desnky-navy"><dt>Total</dt><dd>NGN <?php echo number_format($totals['total']); ?></dd></div>
            </dl>
            <a href="/shop/checkout" class="btn-primary mt-6 w-full <?php echo empty($items) ? 'pointer-events-none opacity-50' : ''; ?>">Proceed to Checkout</a>
        </aside>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('#cart-update-form', {
        endpoint: '/shop/cart/update',
        resetOnSuccess: false,
        onSuccess: function () {
            window.location.reload();
        }
    });
});
</script>
