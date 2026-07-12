<?php
$product    ??= [];
$history    ??= [];
$categories ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/products" class="text-sm font-semibold text-blue-700">← Back to products</a>
</div>

<form
    id="product-form"
    method="post"
    action="<?php echo empty($product['id']) ? '/admin/products' : '/admin/products/' . (int) $product['id']; ?>"
    class="grid gap-6 lg:grid-cols-[1fr_22rem]"
>
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

    <!-- ── Main column ───────────────────────────────────────────────────── -->
    <div class="space-y-6">

        <!-- Core details -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Product details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Product name <span class="text-red-600">*</span></span>
                    <input id="product-name" name="name" required value="<?php echo $this->escape((string) ($product['name'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. Industrial Safety Gloves">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Slug</span>
                    <input id="product-slug" name="slug" value="<?php echo $this->escape((string) ($product['slug'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="auto-generated">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">SKU <span class="text-red-600">*</span></span>
                    <input name="sku" required value="<?php echo $this->escape((string) ($product['sku'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. DGR-SG-001">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Category</span>
                    <select name="category_id" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="">— None —</option>
                        <?php foreach ($categories as $cat) : ?>
                            <option value="<?php echo (int) $cat['id']; ?>" <?php echo (string) ($product['category_id'] ?? '') === (string) $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $this->escape((string) $cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Short description</span>
                    <textarea name="short_description" rows="2" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Used in product cards and listings."><?php echo $this->escape((string) ($product['short_description'] ?? '')); ?></textarea>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Full description</span>
                    <textarea name="description" rows="8" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Detailed product description."><?php echo $this->escape((string) ($product['description'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>

        <!-- Inventory history (edit mode only) -->
        <?php if (!empty($product['id'])) : ?>
            <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                <h2 class="font-semibold text-gray-950">Inventory history</h2>
                <div class="mt-3 divide-y divide-gray-100 text-sm">
                    <?php foreach ($history as $move) : ?>
                        <div class="flex justify-between py-2">
                            <span><?php echo $this->escape((string) $move['movement_type']); ?> (<?php echo (int) $move['quantity_change']; ?>)</span>
                            <span class="text-gray-400"><?php echo $this->escape((string) $move['created_at']); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($history)) : ?>
                        <p class="py-4 text-gray-400">No stock movements yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

    </div><!-- /main column -->

    <!-- ── Sidebar ───────────────────────────────────────────────────────── -->
    <aside class="space-y-5">

        <!-- 1. Publishing -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Publishing</h2>
            <div class="mt-4 space-y-3">
                <select name="status" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <?php foreach (['active' => 'Active', 'inactive' => 'Inactive', 'discontinued' => 'Discontinued'] as $val => $label) : ?>
                        <option value="<?php echo $val; ?>" <?php echo ($product['status'] ?? 'active') === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Active
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_featured" value="1" <?php echo !empty($product['is_featured']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Featured
                </label>
            </div>
            <button class="mt-5 w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save product</button>
        </section>

        <!-- 2. SEO -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">SEO</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">SEO title</span>
                    <input name="meta_title" value="<?php echo $this->escape((string) ($product['meta_title'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Leave blank to use product name">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Meta description</span>
                    <textarea name="meta_description" rows="3" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Up to 160 characters"><?php echo $this->escape((string) ($product['meta_description'] ?? '')); ?></textarea>
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">SEO image</span>
                    <?php $seoImg = (string) ($product['og_image'] ?? ''); ?>
                    <img id="seo_img_preview" src="<?php echo $this->escape($seoImg); ?>" alt="" class="<?php echo $seoImg ? '' : 'hidden'; ?> mb-2 mt-1 w-full rounded object-cover" style="max-height:100px">
                    <input type="hidden" id="og_image" name="og_image" value="<?php echo $this->escape($seoImg); ?>">
                    <button type="button" onclick="openMediaPicker('og_image','seo_img_preview')" class="w-full rounded border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        <?php echo $seoImg ? 'Change SEO image' : 'Select SEO image'; ?>
                    </button>
                </label>
            </div>
        </section>

        <!-- 3. Media -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Product image</h2>
            <div class="mt-4 space-y-3">
                <?php $fi = (string) ($product['featured_image'] ?? ''); ?>
                <img
                    id="featured_img_preview"
                    src="<?php echo $this->escape($fi); ?>"
                    alt="Product image preview"
                    class="<?php echo $fi ? '' : 'hidden'; ?> w-full rounded object-cover"
                    style="max-height:160px"
                >
                <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $this->escape($fi); ?>">
                <button
                    type="button"
                    onclick="openMediaPicker('featured_image','featured_img_preview')"
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <?php echo $fi ? 'Change image' : 'Select from library'; ?>
                </button>
                <?php if ($fi) : ?>
                    <button type="button" onclick="document.getElementById('featured_image').value='';document.getElementById('featured_img_preview').classList.add('hidden');" class="w-full rounded border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Remove image</button>
                <?php endif; ?>
            </div>
        </section>

        <!-- 4. Pricing and stock -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Pricing &amp; stock</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Regular price <span class="text-red-600">*</span></span>
                    <input name="price" type="number" step="0.01" min="0" required value="<?php echo $this->escape((string) ($product['price'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="0.00">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Discount price</span>
                    <input name="discount_price" type="number" step="0.01" min="0" value="<?php echo $this->escape((string) ($product['discount_price'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="0.00">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Cost price</span>
                    <input name="cost_price" type="number" step="0.01" min="0" value="<?php echo $this->escape((string) ($product['cost_price'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="0.00">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Stock quantity</span>
                    <input name="quantity_in_stock" type="number" min="0" value="<?php echo $this->escape((string) ($product['quantity_in_stock'] ?? 0)); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Reorder level</span>
                    <input name="reorder_level" type="number" min="0" value="<?php echo $this->escape((string) ($product['reorder_level'] ?? 10)); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Weight (kg)</span>
                    <input name="weight" type="number" step="0.01" min="0" value="<?php echo $this->escape((string) ($product['weight'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
            </div>
        </section>

    </aside>
</form>

<?php echo $this->partial('admin/partials/media-picker'); ?>

<script>
(function () {
    const nameInput = document.getElementById('product-name');
    const slugInput = document.getElementById('product-slug');
    let slugEdited  = slugInput.value !== '';

    function toSlug(str) {
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    nameInput.addEventListener('input', () => {
        if (!slugEdited) slugInput.value = toSlug(nameInput.value);
    });
    slugInput.addEventListener('input', () => { slugEdited = slugInput.value !== ''; });
})();
</script>
