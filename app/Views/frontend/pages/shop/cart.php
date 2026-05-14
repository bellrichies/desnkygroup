<?php
$hero = $hero ?? [];
$cart = $cart ?? [];
$items = $items ?? [];
$totals = $totals ?? ['subtotal' => 0, 'shipping' => 0, 'total' => 0];
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
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_22rem]">
        <?php if (empty($items)) : ?>
            <div class="bg-desnky-surface p-8">
                <h2 class="text-2xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($cart['empty_heading'] ?? '')); ?></h2>
                <p class="mt-3 text-desnky-muted"><?php echo $this->escape((string) ($cart['empty_text'] ?? '')); ?></p>
                <a href="/shop" class="btn-primary mt-6"><?php echo $this->escape((string) ($cart['continue_shopping_label'] ?? '')); ?></a>
            </div>
        <?php else : ?>
            <form id="cart-update-form" action="/shop/cart/update" method="POST" class="overflow-x-auto">
                <input type="hidden" name="_token" value="<?php echo $this->escape((string) $csrf_token); ?>">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-desnky-surface text-left text-desnky-navy">
                        <tr>
                            <th class="px-4 py-3"><?php echo $this->escape((string) ($cart['product_heading'] ?? '')); ?></th>
                            <th class="px-4 py-3"><?php echo $this->escape((string) ($cart['price_heading'] ?? '')); ?></th>
                            <th class="px-4 py-3"><?php echo $this->escape((string) ($cart['quantity_heading'] ?? '')); ?></th>
                            <th class="px-4 py-3"><?php echo $this->escape((string) ($cart['subtotal_heading'] ?? '')); ?></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-4">
                                        <img src="<?php echo $this->escape((string) $item['image']); ?>" alt="<?php echo $this->escape((string) $item['name']); ?>" class="h-16 w-16 object-cover" loading="lazy">
                                        <div>
                                            <p class="font-semibold text-desnky-navy"><?php echo $this->escape((string) $item['name']); ?></p>
                                            <p class="text-desnky-muted"><?php echo $this->escape((string) $item['sku']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4"><?php echo $this->escape((string) ($cart['currency_label'] ?? '')); ?> <?php echo number_format((float) $item['price']); ?></td>
                                <td class="px-4 py-4">
                                    <input type="number" min="0" max="<?php echo (int) $item['stock']; ?>" name="quantities[<?php echo $this->escape((string) $item['slug']); ?>]" value="<?php echo (int) $item['quantity']; ?>" class="form-field w-24">
                                </td>
                                <td class="px-4 py-4 font-semibold text-desnky-navy"><?php echo $this->escape((string) ($cart['currency_label'] ?? '')); ?> <?php echo number_format((float) $item['subtotal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="btn-secondary"><?php echo $this->escape((string) ($cart['update_cart_label'] ?? '')); ?></button>
                    <a href="/shop" class="btn-secondary"><?php echo $this->escape((string) ($cart['continue_shopping_label'] ?? '')); ?></a>
                </div>
            </form>
        <?php endif; ?>

        <aside class="h-fit bg-desnky-surface p-6">
            <h2 class="text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($cart['summary_heading'] ?? '')); ?></h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between"><dt><?php echo $this->escape((string) ($cart['subtotal_label'] ?? '')); ?></dt><dd><?php echo $this->escape((string) ($cart['currency_label'] ?? '')); ?> <?php echo number_format((float) $totals['subtotal']); ?></dd></div>
                <div class="flex justify-between"><dt><?php echo $this->escape((string) ($cart['shipping_label'] ?? '')); ?></dt><dd><?php echo $this->escape((string) ($cart['currency_label'] ?? '')); ?> <?php echo number_format((float) $totals['shipping']); ?></dd></div>
                <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold text-desnky-navy"><dt><?php echo $this->escape((string) ($cart['total_label'] ?? '')); ?></dt><dd><?php echo $this->escape((string) ($cart['currency_label'] ?? '')); ?> <?php echo number_format((float) $totals['total']); ?></dd></div>
            </dl>
            <a href="/shop/checkout" class="btn-primary mt-6 w-full <?php echo empty($items) ? 'pointer-events-none opacity-50' : ''; ?>"><?php echo $this->escape((string) ($cart['checkout_label'] ?? '')); ?></a>
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
