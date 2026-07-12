<?php
$listing = $listing ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
$currentCategory = $currentCategory ?? null;
$currency = (string) ($listing['currency_label'] ?? 'NGN');
$csrf = (string) ($csrf_token ?? '');
$productCount = count($products);
$activeCategoryName = (string) ($listing['all_products_label'] ?? 'All products');

foreach ($categories as $category) {
    if (($category['slug'] ?? null) === $currentCategory) {
        $activeCategoryName = (string) ($category['name'] ?? $activeCategoryName);
        break;
    }
}
?>

<section class="section-band bg-white">
    <div class="container-page" data-shop-page>
        <div class="mb-8 max-w-3xl" data-reveal>
            <p class="eyebrow">Shop</p>
            <h1 class="mt-3 section-heading"><?php echo $this->escape((string) ($title ?? 'Shop')); ?></h1>
            <p class="section-lead"><?php echo $this->escape((string) ($listing['text'] ?? 'Browse practical products and supplies for business, site and field operations.')); ?></p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[18rem_minmax(0,1fr)]">
            <aside class="hidden lg:block">
                <div class="shop-filter-panel">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark"><?php echo $this->escape((string) ($listing['categories_heading'] ?? 'Categories')); ?></h2>
                        <nav class="mt-4 grid gap-1.5" aria-label="<?php echo $this->escape((string) ($listing['categories_heading'] ?? 'Product categories')); ?>">
                            <a href="/shop" data-shop-category-link class="shop-category-link <?php echo $currentCategory === null ? 'shop-category-link--active' : ''; ?>">
                                <?php echo $this->escape((string) ($listing['all_products_label'] ?? 'All products')); ?>
                            </a>
                            <?php foreach ($categories as $category) : ?>
                                <a href="/shop/category/<?php echo $this->escape((string) $category['slug']); ?>" data-shop-category-link class="shop-category-link <?php echo $currentCategory === $category['slug'] ? 'shop-category-link--active' : ''; ?>">
                                    <?php echo $this->escape((string) $category['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>

                    <div class="rounded-lg bg-desnky-primary-50 p-4 text-sm leading-6 text-desnky-muted">
                        <p class="font-semibold text-desnky-dark">Need bulk or B2B pricing?</p>
                        <p class="mt-1">Send the product list and quantity required for a tailored quote.</p>
                        <a href="/contact" class="btn-ghost mt-3">Request a quote <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?></a>
                    </div>
                </div>
            </aside>

            <div>
                <div class="mb-8 rounded-lg border border-gray-200 bg-desnky-surface p-4 sm:p-5 lg:hidden" x-data="{ open: false }">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-desnky-primary"><?php echo $this->escape($activeCategoryName); ?></p>
                            <p class="mt-1 text-2xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($title ?? 'Shop')); ?></p>
                        </div>
                        <button type="button" class="btn-secondary shrink-0" @click="open = true">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'search', 'class' => 'h-5 w-5']); ?>
                            Filter
                        </button>
                    </div>

                    <div x-cloak x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 z-header bg-desnky-dark/55 lg:hidden"></div>
                    <aside
                        x-cloak
                        class="fixed inset-y-0 left-0 z-[60] w-[min(22rem,92vw)] overflow-y-auto bg-white p-6 shadow-2xl transition-transform duration-base ease-brand lg:hidden"
                        :class="open ? 'translate-x-0' : '-translate-x-full'"
                        aria-label="Mobile product filters"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <h2 class="text-lg font-bold text-desnky-dark">Filters</h2>
                            <button type="button" class="btn-icon" @click="open = false" aria-label="Close filters">
                                <?php echo $this->partial('frontend/partials/icon', ['name' => 'close', 'class' => 'h-6 w-6']); ?>
                            </button>
                        </div>

                        <label for="shop-search-mobile" class="form-label mt-6"><?php echo $this->escape((string) ($listing['search_label'] ?? 'Search products')); ?></label>
                        <input id="shop-search-mobile" type="search" class="form-field" placeholder="<?php echo $this->escape((string) ($listing['search_placeholder'] ?? 'Search products')); ?>" data-shop-search>

                        <h3 class="mt-6 text-sm font-bold uppercase tracking-wide text-desnky-dark"><?php echo $this->escape((string) ($listing['categories_heading'] ?? 'Categories')); ?></h3>
                        <nav class="mt-4 grid gap-1.5" aria-label="Mobile product categories">
                            <a href="/shop" data-shop-category-link class="shop-category-link <?php echo $currentCategory === null ? 'shop-category-link--active' : ''; ?>">
                                <?php echo $this->escape((string) ($listing['all_products_label'] ?? 'All products')); ?>
                            </a>
                            <?php foreach ($categories as $category) : ?>
                                <a href="/shop/category/<?php echo $this->escape((string) $category['slug']); ?>" data-shop-category-link class="shop-category-link <?php echo $currentCategory === $category['slug'] ? 'shop-category-link--active' : ''; ?>">
                                    <?php echo $this->escape((string) $category['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </aside>
                </div>

                <div class="mb-6 hidden lg:block" data-reveal>
                    <p class="text-sm font-semibold text-desnky-primary"><?php echo $this->escape($activeCategoryName); ?></p>
                    <h2 class="mt-2 text-2xl font-bold text-desnky-dark"><?php echo $this->escape((string) ($listing['heading'] ?? 'Available products')); ?></h2>
                </div>

                <div class="shop-toolbar" data-reveal>
                    <div class="relative min-w-0 flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-desnky-muted">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'search', 'class' => 'h-5 w-5']); ?>
                        </span>
                        <label class="sr-only" for="shop-search"><?php echo $this->escape((string) ($listing['search_label'] ?? 'Search products')); ?></label>
                        <input id="shop-search" type="search" class="form-field pl-10" placeholder="<?php echo $this->escape((string) ($listing['search_placeholder'] ?? 'Search products')); ?>" data-shop-search>
                    </div>
                    <label class="sr-only" for="shop-sort"><?php echo $this->escape((string) ($listing['sort_label'] ?? 'Sort')); ?></label>
                    <select id="shop-sort" class="form-field w-full sm:w-56" data-shop-sort>
                        <option value="name"><?php echo $this->escape((string) ($listing['sort_name_label'] ?? 'Name')); ?></option>
                        <option value="price-low"><?php echo $this->escape((string) ($listing['sort_price_low_label'] ?? 'Price: low to high')); ?></option>
                        <option value="price-high"><?php echo $this->escape((string) ($listing['sort_price_high_label'] ?? 'Price: high to low')); ?></option>
                    </select>
                </div>

                <div class="mt-4 flex items-center justify-between gap-4">
                    <p class="text-sm text-desnky-muted" data-shop-results-count data-count-label="<?php echo $this->escape((string) ($listing['count_label'] ?? '{count} products')); ?>">
                        <?php echo $this->escape(str_replace('{count}', (string) $productCount, (string) ($listing['count_label'] ?? '{count} products'))); ?>
                    </p>
                    <button type="button" class="hidden text-sm font-semibold text-desnky-primary hover:text-desnky-primary-700" data-shop-clear>Clear search</button>
                </div>

                <div class="relative mt-6">
                    <div class="shop-loading hidden" data-shop-loading role="status" aria-live="polite">
                        <span class="h-8 w-8 animate-spin rounded-full border-4 border-desnky-primary border-t-transparent"></span>
                        <span class="sr-only">Loading products</span>
                    </div>

                    <?php if ($products !== []) : ?>
                        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3" data-shop-grid>
                            <?php foreach ($products as $product) : ?>
                                <?php $inStock = !empty($product['in_stock']); ?>
                                <?php $hasPrice = isset($product['price']) && $product['price'] !== ''; ?>
                                <article
                                    class="product-card"
                                    data-product-card
                                    data-name="<?php echo $this->escape(strtolower((string) $product['name'])); ?>"
                                    data-price="<?php echo (float) $product['price']; ?>"
                                    data-category="<?php echo $this->escape(strtolower((string) ($product['category'] ?? ''))); ?>"
                                    data-reveal
                                >
                                    <a href="/shop/product/<?php echo $this->escape((string) $product['slug']); ?>" class="product-card__media">
                                        <?php if (!empty($product['image'])) : ?>
                                            <img src="<?php echo $this->escape((string) $product['image']); ?>" alt="<?php echo $this->escape((string) $product['name']); ?>" class="product-card__image" loading="lazy">
                                        <?php else : ?>
                                            <div class="product-card__image product-card__image--empty">No image</div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="product-card__content">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="product-card__category"><?php echo $this->escape((string) $product['category']); ?></p>
                                            <span class="product-card__stock <?php echo $inStock ? 'product-card__stock--in' : 'product-card__stock--out'; ?>">
                                                <?php echo $this->escape((string) ($inStock ? ($listing['in_stock_label'] ?? 'In stock') : ($listing['out_of_stock_label'] ?? 'Out of stock'))); ?>
                                            </span>
                                        </div>
                                        <h3 class="product-card__title">
                                            <a href="/shop/product/<?php echo $this->escape((string) $product['slug']); ?>"><?php echo $this->escape((string) $product['name']); ?></a>
                                        </h3>
                                        <?php if (!empty($product['short_description'])) : ?>
                                            <p class="product-card__summary"><?php echo $this->escape((string) $product['short_description']); ?></p>
                                        <?php endif; ?>
                                        <div class="product-card__meta">
                                            <?php if ($hasPrice) : ?>
                                                <span class="product-card__price"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['price']); ?></span>
                                            <?php else : ?>
                                                <span class="product-card__price">Request quote</span>
                                            <?php endif; ?>
                                            <?php if (!empty($product['old_price'])) : ?>
                                                <span class="product-card__old-price"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['old_price']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-card__actions">
                                            <a href="/shop/product/<?php echo $this->escape((string) $product['slug']); ?>" class="product-card__view"><?php echo $this->escape((string) ($listing['view_label'] ?? 'View')); ?></a>
                                            <?php if ($inStock) : ?>
                                                <form action="/shop/cart/add" method="POST" data-add-to-cart>
                                                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                                                    <input type="hidden" name="slug" value="<?php echo $this->escape((string) $product['slug']); ?>">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="product-card__cart"><?php echo $this->escape((string) ($listing['add_to_cart_label'] ?? 'Add to cart')); ?></button>
                                                </form>
                                            <?php else : ?>
                                                <button type="button" class="product-card__cart" disabled><?php echo $this->escape((string) ($listing['out_of_stock_label'] ?? 'Out of stock')); ?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-state">
                            <p class="text-lg font-bold text-desnky-dark">No products found.</p>
                            <p class="mt-2 text-sm text-desnky-muted">Try another category or browse the full catalogue.</p>
                            <a href="/shop" class="btn-primary mt-5">Browse all products</a>
                        </div>
                    <?php endif; ?>

                    <div class="empty-state hidden" data-shop-empty>
                        <p class="text-lg font-bold text-desnky-dark">No matching products.</p>
                        <p class="mt-2 text-sm text-desnky-muted">Adjust your search term or clear the filter to see all available products.</p>
                        <button type="button" class="btn-primary mt-5" data-shop-clear>Clear search</button>
                    </div>
                </div>

                <div class="mt-12 grid gap-4 rounded-lg border border-gray-200 bg-desnky-surface p-5 sm:grid-cols-3" data-reveal>
                    <div class="flex items-center gap-3"><span class="icon-tile-success"><?php echo $this->partial('frontend/partials/icon', ['name' => 'truck', 'class' => 'h-5 w-5']); ?></span><p class="text-sm font-semibold text-desnky-dark">Nationwide delivery</p></div>
                    <div class="flex items-center gap-3"><span class="icon-tile"><?php echo $this->partial('frontend/partials/icon', ['name' => 'shield-check', 'class' => 'h-5 w-5']); ?></span><p class="text-sm font-semibold text-desnky-dark">Genuine products</p></div>
                    <div class="flex items-center gap-3"><span class="icon-tile"><?php echo $this->partial('frontend/partials/icon', ['name' => 'check-circle', 'class' => 'h-5 w-5']); ?></span><p class="text-sm font-semibold text-desnky-dark">Quote support</p></div>
                </div>
            </div>
        </div>
    </div>
</section>
