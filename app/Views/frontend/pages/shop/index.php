<section class="section-band bg-desnky-surface">
    <div class="container-page">
        <nav class="text-sm text-desnky-muted" aria-label="Breadcrumb">
            <a href="/" class="hover:text-desnky-blue">Home</a> / <span>Shop</span>
        </nav>
        <div class="mt-6 max-w-3xl">
            <h1 class="text-4xl font-bold text-desnky-navy sm:text-5xl"><?php echo $this->escape($title); ?></h1>
            <p class="mt-5 text-lg leading-8 text-desnky-muted">
                Browse safety equipment, ICT devices, industrial supplies and agro product options.
            </p>
        </div>
    </div>
</section>

<section class="section-band">
    <div class="container-page grid gap-8 lg:grid-cols-[18rem_1fr]">
        <aside class="space-y-6">
            <div>
                <label for="shop-search" class="form-label">Search products</label>
                <input id="shop-search" type="search" class="form-field" placeholder="Search by name or SKU" data-shop-search>
            </div>
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wide text-desnky-navy">Categories</h2>
                <div class="mt-4 grid gap-2">
                    <a href="/shop" class="<?php echo $currentCategory === null ? 'bg-desnky-navy text-white' : 'border border-gray-200 text-desnky-navy'; ?> rounded-md px-4 py-2 text-sm font-semibold">All products</a>
                    <?php foreach ($categories as $slug => $label) : ?>
                        <a href="/shop/category/<?php echo $this->escape($slug); ?>" class="<?php echo $currentCategory === $slug ? 'bg-desnky-navy text-white' : 'border border-gray-200 text-desnky-navy'; ?> rounded-md px-4 py-2 text-sm font-semibold">
                            <?php echo $this->escape($label); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <div>
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-desnky-muted"><?php echo count($products); ?> products available</p>
                <select class="form-field w-full sm:w-56" data-shop-sort aria-label="Sort products">
                    <option value="name">Sort by name</option>
                    <option value="price-low">Price: low to high</option>
                    <option value="price-high">Price: high to low</option>
                </select>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3" data-shop-grid>
                <?php foreach ($products as $product) : ?>
                    <article class="bg-white shadow-card" data-product-card data-name="<?php echo $this->escape(strtolower($product['name'])); ?>" data-price="<?php echo (int) $product['price']; ?>">
                        <img src="<?php echo $this->escape($product['image']); ?>" alt="<?php echo $this->escape($product['name']); ?>" class="h-56 w-full object-cover" loading="lazy">
                        <div class="p-6">
                            <p class="text-xs font-bold uppercase tracking-wide text-desnky-blue"><?php echo $this->escape($product['category']); ?></p>
                            <h2 class="mt-2 text-lg font-bold text-desnky-navy"><?php echo $this->escape($product['name']); ?></h2>
                            <p class="mt-2 text-sm leading-6 text-desnky-muted"><?php echo $this->escape($product['short_description']); ?></p>
                            <div class="mt-4 flex items-baseline gap-2">
                                <span class="text-xl font-bold text-desnky-navy">NGN <?php echo number_format($product['price']); ?></span>
                                <?php if ($product['old_price']) : ?>
                                    <span class="text-sm text-desnky-muted line-through">NGN <?php echo number_format($product['old_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="mt-2 text-sm <?php echo $product['in_stock'] ? 'text-desnky-green' : 'text-red-700'; ?>">
                                <?php echo $product['in_stock'] ? 'In stock' : 'Out of stock'; ?>
                            </p>
                            <div class="mt-5 flex gap-3">
                                <a href="/shop/product/<?php echo $this->escape($product['slug']); ?>" class="btn-secondary flex-1">View</a>
                                <a href="/shop/product/<?php echo $this->escape($product['slug']); ?>" class="btn-primary flex-1">Add to Cart</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
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
            var cards = Array.from(grid.querySelectorAll('[data-product-card]'));
            cards.sort(function (a, b) {
                if (sort.value === 'price-low') return Number(a.dataset.price) - Number(b.dataset.price);
                if (sort.value === 'price-high') return Number(b.dataset.price) - Number(a.dataset.price);
                return a.dataset.name.localeCompare(b.dataset.name);
            }).forEach(function (card) {
                grid.appendChild(card);
            });
        });
    }
});
</script>
