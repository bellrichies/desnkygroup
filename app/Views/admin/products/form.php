<?php
$product    ??= [];
$history    ??= [];
$categories ??= [];
$csrf_token ??= '';
$gallery ??= [];
$galleryItems = [];
$featuredPath = (string) ($product['featured_image'] ?? '');
if ($featuredPath !== '') {
    $galleryItems[] = ['path' => $featuredPath, 'alt' => (string) ($product['name'] ?? 'Product image')];
}
foreach ($gallery as $galleryImage) {
    $path = trim((string) ($galleryImage['path'] ?? ''));
    if ($path === '' || in_array($path, array_column($galleryItems, 'path'), true)) {
        continue;
    }
    $galleryItems[] = [
        'path' => $path,
        'alt' => (string) ($galleryImage['alt_text'] ?? $product['name'] ?? 'Product image'),
    ];
}
$galleryItems = array_slice($galleryItems, 0, 5);
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

        <!-- Search and social presentation -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-sm font-bold text-violet-700">02</span>
                <div>
                    <h2 class="font-semibold text-gray-950">Search &amp; social</h2>
                    <p class="mt-1 text-xs text-gray-500">Control how this product appears in search results and when shared.</p>
                </div>
            </div>
            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">SEO title</span>
                    <input name="meta_title" maxlength="255" value="<?php echo $this->escape((string) ($product['meta_title'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Leave blank to use the product name">
                    <span class="mt-1 block text-xs text-gray-500">Use a concise, descriptive title of approximately 50–60 characters.</span>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Meta description</span>
                    <textarea name="meta_description" rows="3" maxlength="180" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Summarize the product for search results."><?php echo $this->escape((string) ($product['meta_description'] ?? '')); ?></textarea>
                    <span class="mt-1 block text-xs text-gray-500">Recommended length: 140–160 characters.</span>
                </label>
                <div class="md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Social sharing image</span>
                    <?php $seoImg = (string) ($product['og_image'] ?? ''); ?>
                    <div class="mt-2 grid gap-4 rounded-xl border border-gray-200 bg-gray-50 p-4 sm:grid-cols-[9rem_1fr] sm:items-center">
                        <div class="flex h-24 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-white">
                            <img id="seo_img_preview" src="<?php echo $this->escape($seoImg); ?>" alt="Social sharing preview" class="<?php echo $seoImg ? '' : 'hidden'; ?> h-full w-full object-cover">
                            <span id="seo_img_empty" class="<?php echo $seoImg ? 'hidden ' : ''; ?>px-3 text-center text-xs text-gray-400">No social image selected</span>
                        </div>
                        <div>
                            <input type="hidden" id="og_image" name="og_image" value="<?php echo $this->escape($seoImg); ?>">
                            <button type="button" onclick="openMediaPicker('og_image','seo_img_preview');document.getElementById('seo_img_empty').classList.add('hidden')" class="rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 hover:border-blue-400 hover:text-blue-700">
                                <?php echo $seoImg ? 'Change social image' : 'Select from Media Library'; ?>
                            </button>
                            <p class="mt-2 text-xs text-gray-500">Optional. The featured product image is used as the fallback.</p>
                        </div>
                    </div>
                </div>
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
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Product status</span>
                <select name="status" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <?php foreach (['active' => 'Active', 'inactive' => 'Inactive', 'discontinued' => 'Discontinued'] as $val => $label) : ?>
                        <option value="<?php echo $val; ?>" <?php echo ($product['status'] ?? 'active') === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 py-1 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="is_active" value="1" <?php echo ($product['is_active'] ?? 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Active
                    </label>
                    <label class="flex items-center gap-2 py-1 text-sm font-semibold text-gray-700">
                        <input type="checkbox" name="is_featured" value="1" <?php echo !empty($product['is_featured']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Featured
                    </label>
                </div>
            </div>
            <button class="mt-5 w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save product</button>
        </section>

        <!-- 2. Media -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <div class="flex items-start justify-between gap-3">
                <div><h2 class="font-semibold text-gray-950">Product gallery</h2><p class="mt-1 text-xs text-gray-500">Select up to five images and choose one featured image.</p></div>
                <span id="gallery-count" class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">0 / 5</span>
            </div>
            <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $this->escape($featuredPath); ?>">
            <div id="product-gallery" class="mt-4 overflow-hidden rounded-xl border border-gray-200"></div>
            <button type="button" id="select-gallery-images" class="mt-4 w-full rounded-xl bg-blue-700 px-3 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800">Select images from Media Library</button>
            <p id="gallery-error" role="alert" class="mt-2 hidden text-xs font-semibold text-red-600"></p>
        </section>

        <!-- 3. Pricing and stock -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Pricing &amp; stock</h2>
            <div class="mt-4 grid grid-cols-2 gap-3">
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

    const galleryRoot = document.getElementById('product-gallery');
    const featuredInput = document.getElementById('featured_image');
    const galleryCount = document.getElementById('gallery-count');
    const galleryError = document.getElementById('gallery-error');
    let galleryImages = <?php echo json_encode($galleryItems, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    const escapeHtml = value => String(value || '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[character]);

    function renderGallery() {
        if (galleryImages.length > 0 && !galleryImages.some(image => image.path === featuredInput.value)) {
            featuredInput.value = galleryImages[0].path;
        }
        if (galleryImages.length === 0) {
            featuredInput.value = '';
            galleryRoot.innerHTML = '<div class="bg-gray-50 p-5 text-center text-xs text-gray-500">No product images selected.</div>';
        } else {
            galleryRoot.innerHTML = `
                <div class="grid grid-cols-[3.25rem_1fr] items-center gap-3 border-b border-gray-200 bg-gray-50 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-gray-500">
                    <span>Image</span><span class="text-right">Actions</span>
                </div>
                <div class="divide-y divide-gray-100">
                    ${galleryImages.map((image, index) => `
                        <article class="grid grid-cols-[3.25rem_1fr] items-center gap-3 px-3 py-2.5 ${image.path === featuredInput.value ? 'bg-blue-50/70' : 'bg-white'}">
                            <img src="${escapeHtml(image.path)}" alt="${escapeHtml(image.alt)}" class="h-11 w-11 rounded-lg border ${image.path === featuredInput.value ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-200'} object-cover">
                            <div class="flex items-center justify-end gap-1.5">
                                <label class="cursor-pointer rounded-lg border px-2 py-1.5 text-[10px] font-bold ${image.path === featuredInput.value ? 'border-blue-200 bg-blue-100 text-blue-700' : 'border-gray-200 bg-white text-gray-600 hover:border-blue-300'}" title="Set as featured image">
                                    <input type="radio" name="featured_choice" value="${escapeHtml(image.path)}" ${image.path === featuredInput.value ? 'checked' : ''} data-feature-index="${index}" class="sr-only">
                                    ${image.path === featuredInput.value ? '✓' : 'Set'}
                                </label>
                                <button type="button" data-remove-index="${index}" class="rounded-lg border border-red-100 bg-red-50 px-2 py-1.5 text-[10px] font-bold text-red-600 hover:bg-red-100" title="Remove image">Remove</button>
                            </div>
                            <input type="hidden" name="gallery_paths[]" value="${escapeHtml(image.path)}">
                            <input type="hidden" name="gallery_alt_texts[]" value="${escapeHtml(image.alt)}">
                        </article>
                    `).join('')}
                </div>
            `;
        }
        galleryCount.textContent = `${galleryImages.length} / 5`;
        galleryError.classList.add('hidden');
    }

    galleryRoot.addEventListener('change', event => {
        const radio = event.target.closest('[data-feature-index]');
        if (!radio) return;
        featuredInput.value = galleryImages[Number(radio.dataset.featureIndex)].path;
        renderGallery();
    });
    galleryRoot.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-index]');
        if (!button) return;
        galleryImages.splice(Number(button.dataset.removeIndex), 1);
        renderGallery();
    });
    document.getElementById('select-gallery-images').addEventListener('click', () => {
        openMediaPickerMulti(items => {
            const existing = new Set(galleryImages.map(image => image.path));
            const newItems = items.filter(item => !existing.has(item.path));
            const exceeded = newItems.length > (5 - galleryImages.length);
            items.forEach(item => {
                if (!existing.has(item.path) && galleryImages.length < 5) {
                    galleryImages.push({ path: item.path, alt: item.alt || nameInput.value || 'Product image' });
                    existing.add(item.path);
                }
            });
            renderGallery();
            if (exceeded) {
                galleryError.textContent = 'A product can have a maximum of five images.';
                galleryError.classList.remove('hidden');
            }
        });
    });
    renderGallery();
})();
</script>
