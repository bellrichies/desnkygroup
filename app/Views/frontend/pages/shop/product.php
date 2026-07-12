<?php
$product = $product ?? [];
$detail = $detail ?? [];
$relatedProducts = $relatedProducts ?? [];
$gallery = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
$primaryImage = (string) ($gallery[0]['path'] ?? ($product['image'] ?? ''));
$primaryAlt = (string) ($gallery[0]['alt_text'] ?? ($product['name'] ?? ''));
$currency = (string) ($detail['currency_label'] ?? '₦');
$inStock = !empty($product['in_stock']);
$csrf = (string) ($csrf_token ?? '');
?>

<section class="section-band">
    <div class="container-page">
        <nav class="mb-8 text-sm text-desnky-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="/" class="hover:text-desnky-primary"><?php echo $this->escape((string) ($detail['breadcrumb_home_label'] ?? 'Home')); ?></a></li>
                <li aria-hidden="true">/</li>
                <li><a href="/shop" class="hover:text-desnky-primary"><?php echo $this->escape((string) ($detail['breadcrumb_shop_label'] ?? 'Shop')); ?></a></li>
                <li aria-hidden="true">/</li>
                <li><span class="text-desnky-dark" aria-current="page"><?php echo $this->escape((string) $product['name']); ?></span></li>
            </ol>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2" x-data='{ "main": <?php echo $this->escapeJson($primaryImage); ?>, "alt": <?php echo $this->escapeJson($primaryAlt); ?> }'>
            <!-- Gallery -->
            <div>
                <button type="button" class="block w-full overflow-hidden rounded-lg border border-gray-200" :data-lightbox="main" :data-lightbox-alt="alt" data-lightbox="<?php echo $this->escape($primaryImage); ?>" aria-label="Zoom product image">
                    <img src="<?php echo $this->escape($primaryImage); ?>" :src="main" alt="<?php echo $this->escape($primaryAlt); ?>" :alt="alt" class="aspect-square w-full object-cover" loading="eager" fetchpriority="high">
                </button>
                <?php if (count($gallery) > 1) : ?>
                    <div class="mt-4 grid grid-cols-4 gap-3">
                        <?php foreach ($gallery as $index => $image) : ?>
                            <?php $p = (string) ($image['path'] ?? ''); $a = (string) ($image['alt_text'] ?: $product['name']); ?>
                            <button
                                type="button"
                                class="overflow-hidden rounded-md border-2 transition-colors"
                                :class='main === <?php echo $this->escapeJson($p); ?> ? "border-desnky-primary" : "border-transparent hover:border-gray-300"'
                                @click='main = <?php echo $this->escapeJson($p); ?>; alt = <?php echo $this->escapeJson($a); ?>'
                            >
                                <img src="<?php echo $this->escape($p); ?>" alt="<?php echo $this->escape($a); ?>" class="aspect-square w-full object-cover" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-desnky-primary"><?php echo $this->escape((string) $product['category']); ?></p>
                <h1 class="mt-2 text-3xl font-bold text-desnky-dark sm:text-4xl"><?php echo $this->escape((string) $product['name']); ?></h1>

                <div class="mt-5 flex items-center gap-3">
                    <span class="text-3xl font-bold text-desnky-dark"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['price']); ?></span>
                    <?php if (!empty($product['old_price'])) : ?>
                        <span class="text-base text-desnky-muted line-through"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['old_price']); ?></span>
                    <?php endif; ?>
                    <span class="<?php echo $inStock ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $this->escape((string) ($inStock ? str_replace('{stock}', (string) (int) $product['stock'], (string) ($detail['in_stock_label'] ?? 'In stock')) : ($detail['out_of_stock_label'] ?? 'Out of stock'))); ?>
                    </span>
                </div>

                <p class="mt-5 text-base leading-7 text-desnky-muted"><?php echo $this->escape((string) $product['description']); ?></p>

                <form id="add-to-cart-form" action="/shop/cart/add" method="POST" data-add-to-cart class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-end">
                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                    <input type="hidden" name="slug" value="<?php echo $this->escape((string) $product['slug']); ?>">
                    <div x-data="{ qty: 1, max: <?php echo (int) max(1, $product['stock']); ?> }">
                        <span class="form-label">Quantity</span>
                        <div class="flex items-center rounded-md border border-gray-300">
                            <button type="button" class="btn-icon" @click="qty = Math.max(1, qty - 1)" aria-label="Decrease quantity" <?php echo $inStock ? '' : 'disabled'; ?>><?php echo $this->partial('frontend/partials/icon', ['name' => 'minus', 'class' => 'h-5 w-5']); ?></button>
                            <input type="number" name="quantity" x-model="qty" min="1" max="<?php echo (int) max(1, $product['stock']); ?>" class="w-14 border-0 bg-transparent p-0 text-center font-semibold text-desnky-dark focus:ring-0" <?php echo $inStock ? '' : 'disabled'; ?>>
                            <button type="button" class="btn-icon" @click="qty = Math.min(max, qty + 1)" aria-label="Increase quantity" <?php echo $inStock ? '' : 'disabled'; ?>><?php echo $this->partial('frontend/partials/icon', ['name' => 'plus', 'class' => 'h-5 w-5']); ?></button>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary flex-1" <?php echo $inStock ? '' : 'disabled'; ?>>
                        <?php echo $this->partial('frontend/partials/icon', ['name' => 'cart', 'class' => 'h-5 w-5']); ?>
                        <?php echo $this->escape((string) ($detail['add_to_cart_label'] ?? 'Add to cart')); ?>
                    </button>
                </form>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <h2 class="text-lg font-bold text-desnky-dark"><?php echo $this->escape((string) ($detail['details_heading'] ?? 'Product details')); ?></h2>
                    <dl class="mt-4 grid gap-3 text-sm">
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-2"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($detail['sku_label'] ?? 'SKU')); ?></dt><dd class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $product['sku']); ?></dd></div>
                        <div class="flex justify-between gap-4 border-b border-gray-100 pb-2"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($detail['category_label'] ?? 'Category')); ?></dt><dd class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $product['category']); ?></dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-desnky-muted"><?php echo $this->escape((string) ($detail['payment_label'] ?? 'Payment')); ?></dt><dd class="font-semibold text-desnky-dark"><?php echo $this->escape((string) ($detail['payment_value'] ?? 'Pay on delivery / Bank transfer')); ?></dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($relatedProducts)) : ?>
<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <h2 class="section-heading"><?php echo $this->escape((string) ($detail['related_heading'] ?? 'You may also like')); ?></h2>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach (array_slice($relatedProducts, 0, 3) as $related) : ?>
                <a href="/shop/product/<?php echo $this->escape((string) $related['slug']); ?>" class="card-interactive block" data-reveal>
                    <img src="<?php echo $this->escape((string) $related['image']); ?>" alt="<?php echo $this->escape((string) $related['name']); ?>" class="aspect-square w-full object-cover" loading="lazy">
                    <div class="card-body">
                        <h3 class="font-bold text-desnky-dark"><?php echo $this->escape((string) $related['name']); ?></h3>
                        <p class="mt-2 text-base font-bold text-desnky-primary"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $related['price']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Sticky mobile add-to-cart bar (sits above the global action bar) -->
<?php if ($inStock) : ?>
<div class="fixed inset-x-0 bottom-16 z-40 flex items-center justify-between gap-3 border-t border-gray-200 bg-white px-4 py-3 shadow-[0_-8px_30px_rgba(29,18,40,0.12)] lg:hidden">
    <div>
        <p class="text-xs text-desnky-muted"><?php echo $this->escape((string) $product['name']); ?></p>
        <p class="text-lg font-bold text-desnky-dark"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['price']); ?></p>
    </div>
    <button type="submit" form="add-to-cart-form" class="btn-primary">
        <?php echo $this->partial('frontend/partials/icon', ['name' => 'cart', 'class' => 'h-5 w-5']); ?>
        Add to cart
    </button>
</div>
<?php endif; ?>
