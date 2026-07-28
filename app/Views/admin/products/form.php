<?php $product = $product ?? []; ?>
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/products" class="text-sm font-semibold text-blue-700">Back to products</a>
</div>

<form method="post" action="<?php echo empty($product['id']) ? '/admin/products' : '/admin/products/' . (int) $product['id']; ?>" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    <div class="space-y-6">
        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Product details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <input name="name" required value="<?php echo $this->escape((string) ($product['name'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Product name">
                <input name="slug" value="<?php echo $this->escape((string) ($product['slug'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Slug">
                <input name="sku" required value="<?php echo $this->escape((string) ($product['sku'] ?? '')); ?>" class="rounded border-gray-300" placeholder="SKU">
                <select name="category_id" class="rounded border-gray-300">
                    <option value="">Select category</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo (int) $category['id']; ?>" <?php echo (string) ($product['category_id'] ?? '') === (string) $category['id'] ? 'selected' : ''; ?>>
                            <?php echo $this->escape((string) $category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <textarea name="short_description" class="rounded border-gray-300 md:col-span-2" placeholder="Short description"><?php echo $this->escape((string) ($product['short_description'] ?? '')); ?></textarea>
                <textarea name="description" rows="6" class="rounded border-gray-300 md:col-span-2" placeholder="Full description"><?php echo $this->escape((string) ($product['description'] ?? '')); ?></textarea>
            </div>
        </section>

        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">SEO and media</h2>
            <div class="mt-4 grid gap-4">
                <input name="featured_image" value="<?php echo $this->escape((string) ($product['featured_image'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Featured image URL">
                <input name="meta_title" value="<?php echo $this->escape((string) ($product['meta_title'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Meta title">
                <textarea name="meta_description" class="rounded border-gray-300" placeholder="Meta description"><?php echo $this->escape((string) ($product['meta_description'] ?? '')); ?></textarea>
            </div>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Pricing and stock</h2>
            <div class="mt-4 grid gap-3">
                <input name="price" type="number" step="0.01" min="0" required value="<?php echo $this->escape((string) ($product['price'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Regular price">
                <input name="discount_price" type="number" step="0.01" min="0" value="<?php echo $this->escape((string) ($product['discount_price'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Discount price">
                <input name="cost_price" type="number" step="0.01" min="0" value="<?php echo $this->escape((string) ($product['cost_price'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Cost price">
                <input name="quantity_in_stock" type="number" min="0" value="<?php echo $this->escape((string) ($product['quantity_in_stock'] ?? 0)); ?>" class="rounded border-gray-300" placeholder="Stock quantity">
                <input name="reorder_level" type="number" min="0" value="<?php echo $this->escape((string) ($product['reorder_level'] ?? 10)); ?>" class="rounded border-gray-300" placeholder="Reorder level">
                <input name="weight" type="number" step="0.01" min="0" value="<?php echo $this->escape((string) ($product['weight'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Weight">
            </div>
        </section>

        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Publishing</h2>
            <select name="status" class="mt-4 w-full rounded border-gray-300">
                <?php foreach (['active', 'inactive', 'discontinued'] as $status) : ?>
                    <option value="<?php echo $status; ?>" <?php echo ($product['status'] ?? 'active') === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                <?php endforeach; ?>
            </select>
            <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?>> Active</label>
            <label class="mt-2 flex items-center gap-2 text-sm"><input type="checkbox" name="is_featured" <?php echo ($product['is_featured'] ?? 0) ? 'checked' : ''; ?>> Featured</label>
            <button class="mt-5 w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save product</button>
        </section>
    </aside>
</form>

<?php if (!empty($product['id'])) : ?>
    <section class="mt-6 rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="font-semibold text-gray-950">Inventory history</h2>
        <div class="mt-3 space-y-2 text-sm">
            <?php foreach ($history as $movement) : ?>
                <div class="flex justify-between border-b border-gray-100 py-2">
                    <span><?php echo $this->escape((string) $movement['movement_type']); ?> (<?php echo (int) $movement['quantity_change']; ?>)</span>
                    <span class="text-gray-500"><?php echo $this->escape((string) $movement['created_at']); ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($history)) : ?><p class="text-gray-500">No stock movements yet.</p><?php endif; ?>
        </div>
    </section>
<?php endif; ?>
