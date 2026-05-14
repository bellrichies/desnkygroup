<section class="section-band">
    <div class="container-page">
        <nav class="mb-8 text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue">Home</a> /
            <a href="/shop" class="hover:text-desnky-blue">Shop</a> /
            <span><?php echo $this->escape($product['name']); ?></span>
        </nav>

        <div class="grid gap-10 lg:grid-cols-[1fr_0.9fr]">
            <div>
                <img src="<?php echo $this->escape($product['image']); ?>" alt="<?php echo $this->escape($product['name']); ?>" class="aspect-[4/3] w-full object-cover" loading="eager">
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <?php for ($i = 0; $i < 3; $i++) : ?>
                        <img src="<?php echo $this->escape($product['image']); ?>" alt="<?php echo $this->escape($product['name']); ?> gallery image <?php echo $i + 1; ?>" class="aspect-square w-full object-cover" loading="lazy">
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape($product['category']); ?></p>
                <h1 class="mt-3 text-4xl font-bold text-desnky-navy"><?php echo $this->escape($product['name']); ?></h1>
                <p class="mt-4 text-base leading-7 text-desnky-muted"><?php echo $this->escape($product['description']); ?></p>
                <div class="mt-6 flex items-baseline gap-3">
                    <span class="text-3xl font-bold text-desnky-navy">NGN <?php echo number_format($product['price']); ?></span>
                    <?php if ($product['old_price']) : ?>
                        <span class="text-base text-desnky-muted line-through">NGN <?php echo number_format($product['old_price']); ?></span>
                    <?php endif; ?>
                </div>
                <p class="mt-3 text-sm <?php echo $product['in_stock'] ? 'text-desnky-green' : 'text-red-700'; ?>">
                    <?php echo $product['in_stock'] ? 'In stock: ' . (int) $product['stock'] . ' available' : 'Out of stock'; ?>
                </p>

                <form id="add-to-cart-form" action="/shop/cart/add" method="POST" class="mt-8 grid gap-4 sm:grid-cols-[8rem_1fr]">
                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                    <input type="hidden" name="slug" value="<?php echo $this->escape($product['slug']); ?>">
                    <label>
                        <span class="form-label">Quantity</span>
                        <input type="number" name="quantity" min="1" max="<?php echo (int) max(1, $product['stock']); ?>" value="1" class="form-field" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                    </label>
                    <div class="self-end">
                        <button type="submit" class="btn-primary w-full" <?php echo !$product['in_stock'] ? 'disabled' : ''; ?>>
                            Add to Cart
                        </button>
                    </div>
                </form>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-desnky-navy">Product details</h2>
                    <dl class="mt-4 grid gap-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted">SKU</dt><dd class="font-semibold text-desnky-navy"><?php echo $this->escape($product['sku']); ?></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted">Category</dt><dd class="font-semibold text-desnky-navy"><?php echo $this->escape($product['category']); ?></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted">Payment</dt><dd class="font-semibold text-desnky-navy">Bank transfer or pay on delivery</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedProducts)) : ?>
    <section class="section-band bg-desnky-surface">
        <div class="container-page">
            <h2 class="text-3xl font-bold text-desnky-navy">Related products</h2>
            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <?php foreach (array_slice($relatedProducts, 0, 3) as $related) : ?>
                    <a href="/shop/product/<?php echo $this->escape($related['slug']); ?>" class="block bg-white shadow-card">
                        <img src="<?php echo $this->escape($related['image']); ?>" alt="<?php echo $this->escape($related['name']); ?>" class="h-44 w-full object-cover" loading="lazy">
                        <div class="p-5">
                            <h3 class="font-bold text-desnky-navy"><?php echo $this->escape($related['name']); ?></h3>
                            <p class="mt-2 text-sm text-desnky-muted">NGN <?php echo number_format($related['price']); ?></p>
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
