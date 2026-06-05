<?php
$hero = $hero ?? [];
$listing = $listing ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
$currentCategory = $currentCategory ?? null;
$currency = (string) ($listing['currency_label'] ?? '₦');
$csrf = (string) ($csrf_token ?? '');

$crumbs = [['label' => 'Home', 'href' => '/'], ['label' => 'Shop', 'href' => '/shop']];
?>

<?php echo $this->partial('frontend/partials/page-hero', [
    'hero' => $hero,
    'title' => $title ?? '',
    'fallbackImage' => $page['featured_image'] ?? '',
    'breadcrumbs' => $crumbs,
]); ?>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[18rem_1fr]">
        <!-- Filters: drawer on mobile, sidebar on lg -->
        <div x-data="{ open: false }">
            <button type="button" class="btn-secondary w-full lg:hidden" @click="open = true">
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'search', 'class' => 'h-5 w-5']); ?>
                Search &amp; filter
            </button>

            <!-- Backdrop (mobile) -->
            <div x-cloak x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 z-header bg-desnky-dark/50 lg:hidden"></div>

            <aside
                class="fixed inset-y-0 left-0 z-[60] w-[min(20rem,90vw)] translate-x-0 space-y-6 overflow-y-auto bg-white p-6 shadow-2xl lg:static lg:z-auto lg:w-auto lg:translate-x-0 lg:p-0 lg:shadow-none"
                :class="open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                style="transition: transform 220ms ease"
            >
                <div class="flex items-center justify-between lg:hidden">
                    <span class="text-base font-bold text-desnky-dark">Filter</span>
                    <button type="button" class="btn-icon" @click="open = false"><span class="sr-only">Close</span><?php echo $this->partial('frontend/partials/icon', ['name' => 'close', 'class' => 'h-6 w-6']); ?></button>
                </div>

                <div>
                    <label for="shop-search" class="form-label"><?php echo $this->escape((string) ($listing['search_label'] ?? 'Search products')); ?></label>
                    <input id="shop-search" type="search" class="form-field" placeholder="<?php echo $this->escape((string) ($listing['search_placeholder'] ?? 'Search…')); ?>" data-shop-search>
                </div>
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-dark"><?php echo $this->escape((string) ($listing['categories_heading'] ?? 'Categories')); ?></h2>
                    <div class="mt-4 grid gap-1.5">
                        <a href="/shop" class="rounded-md px-4 py-2 text-sm font-semibold <?php echo $currentCategory === null ? 'bg-desnky-primary text-white' : 'text-desnky-ink hover:bg-desnky-surface'; ?>">
                            <?php echo $this->escape((string) ($listing['all_products_label'] ?? 'All products')); ?>
                        </a>
                        <?php foreach ($categories as $category) : ?>
                            <a href="/shop/category/<?php echo $this->escape((string) $category['slug']); ?>" class="rounded-md px-4 py-2 text-sm font-semibold <?php echo $currentCategory === $category['slug'] ? 'bg-desnky-primary text-white' : 'text-desnky-ink hover:bg-desnky-surface'; ?>">
                                <?php echo $this->escape((string) $category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="rounded-lg bg-desnky-surface p-4 text-sm text-desnky-muted">
                    <p class="font-semibold text-desnky-dark">Need bulk / B2B pricing?</p>
                    <p class="mt-1">Contact us for tailored quotes on volume orders.</p>
                    <a href="/contact" class="btn-ghost mt-2">Request a quote <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?></a>
                </div>
            </aside>
        </div>

        <div>
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-desnky-muted">
                    <?php echo $this->escape(str_replace('{count}', (string) count($products), (string) ($listing['count_label'] ?? '{count} products'))); ?>
                </p>
                <select class="form-field w-full sm:w-56" data-shop-sort aria-label="<?php echo $this->escape((string) ($listing['sort_label'] ?? 'Sort')); ?>">
                    <option value="name"><?php echo $this->escape((string) ($listing['sort_name_label'] ?? 'Name')); ?></option>
                    <option value="price-low"><?php echo $this->escape((string) ($listing['sort_price_low_label'] ?? 'Price: low to high')); ?></option>
                    <option value="price-high"><?php echo $this->escape((string) ($listing['sort_price_high_label'] ?? 'Price: high to low')); ?></option>
                </select>
            </div>

            <?php if ($products !== []) : ?>
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3" data-shop-grid>
                    <?php foreach ($products as $product) : ?>
                        <?php $inStock = !empty($product['in_stock']); ?>
                        <article class="card-interactive flex flex-col" data-product-card data-name="<?php echo $this->escape(strtolower((string) $product['name'])); ?>" data-price="<?php echo (int) $product['price']; ?>" data-reveal>
                            <div class="relative">
                                <a href="/shop/product/<?php echo $this->escape((string) $product['slug']); ?>" class="block overflow-hidden">
                                    <?php if (!empty($product['image'])) : ?>
                                        <img src="<?php echo $this->escape((string) $product['image']); ?>" alt="<?php echo $this->escape((string) $product['name']); ?>" class="aspect-square w-full object-cover transition-transform duration-slow hover:scale-105" loading="lazy">
                                    <?php else : ?>
                                        <div class="aspect-square w-full bg-desnky-surface"></div>
                                    <?php endif; ?>
                                </a>
                                <span class="absolute left-3 top-3 <?php echo $inStock ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $this->escape((string) ($inStock ? ($listing['in_stock_label'] ?? 'In stock') : ($listing['out_of_stock_label'] ?? 'Out of stock'))); ?>
                                </span>
                            </div>
                            <div class="card-body flex flex-1 flex-col">
                                <p class="text-xs font-bold uppercase tracking-wide text-desnky-primary"><?php echo $this->escape((string) $product['category']); ?></p>
                                <h2 class="mt-1.5 text-lg font-bold text-desnky-dark">
                                    <a href="/shop/product/<?php echo $this->escape((string) $product['slug']); ?>" class="hover:text-desnky-primary"><?php echo $this->escape((string) $product['name']); ?></a>
                                </h2>
                                <p class="mt-2 flex-1 text-sm leading-6 text-desnky-muted"><?php echo $this->escape((string) $product['short_description']); ?></p>
                                <div class="mt-4 flex items-baseline gap-2">
                                    <span class="text-xl font-bold text-desnky-dark"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['price']); ?></span>
                                    <?php if (!empty($product['old_price'])) : ?>
                                        <span class="text-sm text-desnky-muted line-through"><?php echo $this->escape($currency); ?> <?php echo number_format((float) $product['old_price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-5 flex gap-3">
                                    <a href="/shop/product/<?php echo $this->escape((string) $product['slug']); ?>" class="btn-secondary flex-1"><?php echo $this->escape((string) ($listing['view_label'] ?? 'View')); ?></a>
                                    <?php if ($inStock) : ?>
                                        <form action="/shop/cart/add" method="POST" data-add-to-cart class="flex-1">
                                            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                                            <input type="hidden" name="slug" value="<?php echo $this->escape((string) $product['slug']); ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn-primary w-full"><?php echo $this->escape((string) ($listing['add_to_cart_label'] ?? 'Add to cart')); ?></button>
                                        </form>
                                    <?php else : ?>
                                        <button type="button" class="btn-primary flex-1" disabled><?php echo $this->escape((string) ($listing['out_of_stock_label'] ?? 'Out of stock')); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="rounded-lg border border-dashed border-gray-300 bg-desnky-surface py-16 text-center">
                    <p class="text-desnky-muted">No products found in this category.</p>
                    <a href="/shop" class="btn-primary mt-5">Browse all products</a>
                </div>
            <?php endif; ?>

            <!-- Trust strip -->
            <div class="mt-12 grid gap-4 rounded-lg border border-gray-200 bg-desnky-surface p-6 sm:grid-cols-3">
                <div class="flex items-center gap-3"><span class="icon-tile-success"><?php echo $this->partial('frontend/partials/icon', ['name' => 'truck', 'class' => 'h-5 w-5']); ?></span><p class="text-sm font-semibold text-desnky-dark">Nationwide delivery</p></div>
                <div class="flex items-center gap-3"><span class="icon-tile"><?php echo $this->partial('frontend/partials/icon', ['name' => 'shield-check', 'class' => 'h-5 w-5']); ?></span><p class="text-sm font-semibold text-desnky-dark">Genuine, certified products</p></div>
                <div class="flex items-center gap-3"><span class="icon-tile"><?php echo $this->partial('frontend/partials/icon', ['name' => 'check-circle', 'class' => 'h-5 w-5']); ?></span><p class="text-sm font-semibold text-desnky-dark">Pay on delivery or transfer</p></div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var search = document.querySelector('[data-shop-search]');
    var sort = document.querySelector('[data-shop-sort]');
    var grid = document.querySelector('[data-shop-grid]');

    if (search) {
        search.addEventListener('input', function () {
            var term = search.value.toLowerCase();
            document.querySelectorAll('[data-product-card]').forEach(function (card) {
                card.classList.toggle('hidden', card.textContent.toLowerCase().indexOf(term) === -1);
            });
        });
    }

    if (sort && grid) {
        sort.addEventListener('change', function () {
            Array.from(grid.querySelectorAll('[data-product-card]')).sort(function (a, b) {
                if (sort.value === 'price-low') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sort.value === 'price-high') return Number(b.dataset.price) - Number(a.dataset.price);
                return a.dataset.name.localeCompare(b.dataset.name);
            }).forEach(function (card) { grid.appendChild(card); });
        });
    }
});
</script>
