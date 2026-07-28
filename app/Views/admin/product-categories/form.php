<?php $category = $category ?? []; ?>
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/product-categories" class="text-sm font-semibold text-blue-700">Back to categories</a>
</div>

<form method="post" action="<?php echo empty($category['id']) ? '/admin/product-categories' : '/admin/product-categories/' . (int) $category['id']; ?>" class="max-w-3xl rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    <div class="grid gap-4 md:grid-cols-2">
        <input name="name" required value="<?php echo $this->escape((string) ($category['name'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Category name">
        <input name="slug" value="<?php echo $this->escape((string) ($category['slug'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Slug">
        <select name="parent_id" class="rounded border-gray-300">
            <option value="">Root category</option>
            <?php foreach ($categories as $option) : ?>
                <?php if (($category['id'] ?? null) === $option['id']) { continue; } ?>
                <option value="<?php echo (int) $option['id']; ?>" <?php echo (string) ($category['parent_id'] ?? '') === (string) $option['id'] ? 'selected' : ''; ?>>
                    <?php echo $this->escape((string) $option['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input name="sort_order" type="number" value="<?php echo $this->escape((string) ($category['sort_order'] ?? 0)); ?>" class="rounded border-gray-300" placeholder="Sort order">
        <input name="image" value="<?php echo $this->escape((string) ($category['image'] ?? '')); ?>" class="rounded border-gray-300 md:col-span-2" placeholder="Category image URL">
        <textarea name="description" class="rounded border-gray-300 md:col-span-2" placeholder="Description"><?php echo $this->escape((string) ($category['description'] ?? '')); ?></textarea>
        <input name="meta_title" value="<?php echo $this->escape((string) ($category['meta_title'] ?? '')); ?>" class="rounded border-gray-300 md:col-span-2" placeholder="Meta title">
        <textarea name="meta_description" class="rounded border-gray-300 md:col-span-2" placeholder="Meta description"><?php echo $this->escape((string) ($category['meta_description'] ?? '')); ?></textarea>
    </div>
    <label class="mt-4 flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" <?php echo ($category['is_active'] ?? 1) ? 'checked' : ''; ?>> Active</label>
    <button class="mt-5 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save category</button>
</form>
