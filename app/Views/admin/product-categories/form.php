<?php
$category   ??= [];
$categories ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/product-categories" class="text-sm font-semibold text-blue-700">← Back to categories</a>
</div>

<form
    method="post"
    action="<?php echo empty($category['id']) ? '/admin/product-categories' : '/admin/product-categories/' . (int) $category['id']; ?>"
    class="grid gap-6 lg:grid-cols-[1fr_22rem]"
>
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

    <!-- ── Main column ───────────────────────────────────────────────────── -->
    <div class="space-y-6">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Category details</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Name <span class="text-red-600">*</span></span>
                    <input id="cat-name" name="name" required value="<?php echo $this->escape((string) ($category['name'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. Safety Equipment">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Slug</span>
                    <input id="cat-slug" name="slug" value="<?php echo $this->escape((string) ($category['slug'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="auto-generated">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Parent category</span>
                    <select name="parent_id" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="">— Root category —</option>
                        <?php foreach ($categories as $opt) : ?>
                            <?php if (($category['id'] ?? null) === $opt['id']) { continue; } ?>
                            <option value="<?php echo (int) $opt['id']; ?>" <?php echo (string) ($category['parent_id'] ?? '') === (string) $opt['id'] ? 'selected' : ''; ?>>
                                <?php echo $this->escape((string) $opt['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Sort order</span>
                    <input name="sort_order" type="number" value="<?php echo $this->escape((string) ($category['sort_order'] ?? 0)); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Description</span>
                    <textarea name="description" rows="3" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Optional category description."><?php echo $this->escape((string) ($category['description'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">SEO</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Meta title</span>
                    <input name="meta_title" value="<?php echo $this->escape((string) ($category['meta_title'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Leave blank to use category name">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Meta description</span>
                    <textarea name="meta_description" rows="3" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Up to 160 characters."><?php echo $this->escape((string) ($category['meta_description'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>
    </div>

    <!-- ── Sidebar ───────────────────────────────────────────────────────── -->
    <aside class="space-y-5">

        <!-- Publishing -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Publishing</h2>
            <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" <?php echo ($category['is_active'] ?? 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Active
            </label>
            <button class="mt-5 w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save category</button>
            <a href="/admin/product-categories" class="mt-2 block w-full rounded border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
        </section>

        <!-- Category image -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Category image</h2>
            <p class="mt-1 text-xs text-gray-400">Displayed on category cards and listing pages.</p>
            <div class="mt-4 space-y-3">
                <?php $img = (string) ($category['image'] ?? ''); ?>
                <img
                    id="cat_img_preview"
                    src="<?php echo $this->escape($img); ?>"
                    alt="Category image preview"
                    class="<?php echo $img ? '' : 'hidden'; ?> w-full rounded object-cover"
                    style="max-height:160px"
                >
                <input type="hidden" id="category_image" name="image" value="<?php echo $this->escape($img); ?>">
                <button
                    type="button"
                    onclick="openMediaPicker('category_image','cat_img_preview')"
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <?php echo $img ? 'Change image' : 'Select from library'; ?>
                </button>
                <?php if ($img) : ?>
                    <button type="button" onclick="document.getElementById('category_image').value='';document.getElementById('cat_img_preview').classList.add('hidden');" class="w-full rounded border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Remove image</button>
                <?php endif; ?>
            </div>
        </section>

    </aside>
</form>

<?php echo $this->partial('admin/partials/media-picker'); ?>

<script>
(function () {
    const nameInput = document.getElementById('cat-name');
    const slugInput = document.getElementById('cat-slug');
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
