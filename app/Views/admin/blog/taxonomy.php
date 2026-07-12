<?php
$categories = $categories ?? [];
$tags = $tags ?? [];
$csrf = (string) ($csrf_token ?? '');
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Blog Categories & Tags</h1>
        <p class="mt-1 text-sm text-gray-600">Manage public filters, internal linking, and related-post signals.</p>
    </div>
    <a href="/admin/blog" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to posts</a>
</div>

<div class="grid gap-6 xl:grid-cols-2">
    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-lg font-bold text-gray-950">Categories</h2>
        <form method="post" action="/admin/blog/categories" class="mt-4 grid gap-3 rounded bg-gray-50 p-4">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
            <input type="hidden" name="is_active" value="0">
            <input name="name" required class="rounded border-gray-300" placeholder="Category name">
            <input name="slug" class="rounded border-gray-300 font-mono text-sm" placeholder="optional-slug">
            <textarea name="description" rows="2" class="rounded border-gray-300" placeholder="Short category description"></textarea>
            <div class="grid gap-3 sm:grid-cols-2">
                <input name="seo_title" class="rounded border-gray-300" placeholder="SEO title">
                <input name="meta_description" class="rounded border-gray-300" placeholder="Meta description">
            </div>
            <div class="flex items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-700">
                    Active
                </label>
                <input name="sort_order" type="number" value="0" class="w-24 rounded border-gray-300" aria-label="Sort order">
                <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Add category</button>
            </div>
        </form>

        <div class="mt-5 space-y-4">
            <?php foreach ($categories as $category) : ?>
                <div class="rounded border border-gray-200 p-4">
                    <form method="post" action="/admin/blog/categories/<?php echo (int) $category['id']; ?>" class="grid gap-3">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <input type="hidden" name="is_active" value="0">
                        <input name="name" required value="<?php echo $this->escape((string) $category['name']); ?>" class="rounded border-gray-300 font-semibold">
                        <input name="slug" value="<?php echo $this->escape((string) $category['slug']); ?>" class="rounded border-gray-300 font-mono text-sm">
                        <textarea name="description" rows="2" class="rounded border-gray-300"><?php echo $this->escape((string) ($category['description'] ?? '')); ?></textarea>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <input name="seo_title" value="<?php echo $this->escape((string) ($category['seo_title'] ?? '')); ?>" class="rounded border-gray-300" placeholder="SEO title">
                            <input name="meta_description" value="<?php echo $this->escape((string) ($category['meta_description'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Meta description">
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_active" value="1" <?php echo !empty($category['is_active']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                                Active
                            </label>
                            <span class="text-xs text-gray-500"><?php echo (int) ($category['post_count'] ?? 0); ?> posts</span>
                            <input name="sort_order" type="number" value="<?php echo (int) ($category['sort_order'] ?? 0); ?>" class="w-24 rounded border-gray-300" aria-label="Sort order">
                            <button class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
                        </div>
                    </form>
                    <form method="post" action="/admin/blog/categories/<?php echo (int) $category['id']; ?>/delete" class="mt-3" onsubmit="return confirm('Delete this category? It will be hidden from new public filtering.')">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <button class="text-sm font-semibold text-red-700 hover:text-red-900">Delete category</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if ($categories === []) : ?>
                <p class="rounded border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">No categories yet.</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-lg font-bold text-gray-950">Tags</h2>
        <form method="post" action="/admin/blog/tags" class="mt-4 grid gap-3 rounded bg-gray-50 p-4">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
            <input name="name" required class="rounded border-gray-300" placeholder="Tag name">
            <input name="slug" class="rounded border-gray-300 font-mono text-sm" placeholder="optional-slug">
            <textarea name="description" rows="2" class="rounded border-gray-300" placeholder="Short tag description"></textarea>
            <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Add tag</button>
        </form>

        <div class="mt-5 space-y-4">
            <?php foreach ($tags as $tag) : ?>
                <div class="rounded border border-gray-200 p-4">
                    <form method="post" action="/admin/blog/tags/<?php echo (int) $tag['id']; ?>" class="grid gap-3">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <input name="name" required value="<?php echo $this->escape((string) $tag['name']); ?>" class="rounded border-gray-300 font-semibold">
                        <input name="slug" value="<?php echo $this->escape((string) $tag['slug']); ?>" class="rounded border-gray-300 font-mono text-sm">
                        <textarea name="description" rows="2" class="rounded border-gray-300"><?php echo $this->escape((string) ($tag['description'] ?? '')); ?></textarea>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs text-gray-500"><?php echo (int) ($tag['post_count'] ?? 0); ?> posts</span>
                            <button class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
                        </div>
                    </form>
                    <form method="post" action="/admin/blog/tags/<?php echo (int) $tag['id']; ?>/delete" class="mt-3" onsubmit="return confirm('Delete this tag?')">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <button class="text-sm font-semibold text-red-700 hover:text-red-900">Delete tag</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if ($tags === []) : ?>
                <p class="rounded border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">No tags yet.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
