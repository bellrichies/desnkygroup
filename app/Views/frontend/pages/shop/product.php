<?php
$product = $product ?? [];
$detail = $detail ?? [];
$relatedProducts = $relatedProducts ?? [];
$gallery = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
$primaryImage = $gallery[0]['path'] ?? ($product['image'] ?? '');
$primaryAlt = $gallery[0]['alt_text'] ?? ($product['name'] ?? '');
?>

<section class="section-band">
    <div class="container-page">
        <nav class="mb-8 text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? '')); ?></a> /
            <a href="/shop" class="hover:text-desnky-blue"><?php echo $this->escape((string) ($detail['breadcrumb_shop_label'] ?? '')); ?></a> /
            <span><?php echo $this->escape((string) $product['name']); ?></span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-[1fr_0.9fr]">
            <div>
                <img src="<?php echo $this->escape((string) $primaryImage); ?>" alt="<?php echo $this->escape((string) $primaryAlt); ?>" class="aspect-[4/3] w-full object-cover" loading="eager">
                <?php if (count($gallery) > 1) : ?>
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <?php foreach ($gallery as $index => $image) : ?>
                            <img src="<?php echo $this->escape((string) $image['path']); ?>" alt="<?php echo $this->escape((string) ($image['alt_text'] ?: $product['name'])); ?>" class="aspect-square w-full object-cover" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape((string) $product['category']); ?></p>
                <h1 class="mt-3 text-4xl font-bold text-desnky-navy"><?php echo $this->escape((string) $product['name']); ?></h1>
                <p class="mt-4 text-base leading-7 text-desnky-muted"><?php echo $this->escape((string) $product['description']); ?></p>
                <div class="mt-6 flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['currency_label'] ?? '')); ?> <?php echo number_format((float) $product['price']); ?></span>
                    <?php if ($product['old_price']) : ?>
                        <span class="text-base text-desnky-muted line-through"><?php echo $this->escape((string) ($detail['currency_label'] ?? '')); ?> <?php echo number_format((float) $product['old_price']); ?></span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-sm <?php echo $product['in_stock'] ? 'text-desnky-green' : 'text-red-700'; ?>">
                    <?php echo $this->escape((string) ($product['in_stock'] ? str_replace('{stock}', (string) (int) $product['stock'], (string) ($detail['in_stock_label'] ?? '')) : ($detail['out_of_stock_label'] ?? ''))); ?>
                </p>

                <form id="add-to-cart-form" action="/shop/cart/add" method="POST" class="mt-8 grid gap-4 sm:grid-cols-[8rem_1fr]">
                    <input type="hidden" name="_token" value="<?php echo $this->escape((string) $csrf_token); ?>">
                    <input type="hidden" name="slug" value="<?php echo $this->escape((string) $product['slug']); ?>">
                    <label>
                        <span class="form-label"><?php echo $this->escape((string) ($detail['quantity_label'] ?? '')); ?></span>
                        <input type="number" name="quantity" min="1" max="<?php echo (int) max(1, $product['stock']); ?>" value="1" class="form-field" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                    </label>
                    <div class="self-end">
                        <button type="submit" class="btn-primary w-full" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                            <?php echo $this->escape((string) ($detail['add_to_cart_label'] ?? '')); ?>
                        </button>
                    </div>
                </form>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['details_heading'] ?? '')); ?></h2>
                    <dl class="mt-4 grid gap-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($detail['sku_label'] ?? '')); ?></dt><dd class="font-semibold text-desnky-navy"><?php echo $this->escape((string) $product['sku']); ?></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($detail['category_label'] ?? '')); ?></dt><dd class="font-semibold text-desnky-navy"><?php echo $this->escape((string) $product['category']); ?></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($detail['payment_label'] ?? '')); ?></dt><dd class="font-semibold text-desnky-navy"><?php echo $this->escape((string) ($detail['payment_value'] ?? '')); ?></dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedProducts)) : ?>
    <section class="section-band bg-desnky-surface">
        <div class="container-page">
            <h2 class="text-3xl font-bold text-desnky-navy"><?php echo $this->escape((string) ($detail['related_heading'] ?? '')); ?></h2>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <?php foreach (array_slice($relatedProducts, 0, 3) as $related) : ?>
                    <a href="/shop/product/<?php echo $this->escape((string) $related['slug']); ?>" class="block bg-white shadow-card">
                        <img src="<?php echo $this->escape((string) $related['image']); ?>" alt="<?php echo $this->escape((string) $related['name']); ?>" class="h-44 w-full object-cover" loading="lazy">
                        <div class="p-5">
                            <h3 class="font-bold text-desnky-navy"><?php echo $this->escape((string) $related['name']); ?></h3>
                            <p class="mt-2 text-sm text-desnky-muted"><?php echo $this->escape((string) ($detail['currency_label'] ?? '')); ?> <?php echo number_format((float) $related['price']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    submitForm('#add-to-cart-form', {
        endpoint: '/shop/cart/add',
        resetOnSuccess: false,
        onSuccess: function () {
            window.setTimeout(function () {
                window.location.href = '/shop/cart';
            }, 500);
        }
    });
});
</script>
