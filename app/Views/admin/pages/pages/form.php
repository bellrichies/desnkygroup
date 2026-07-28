<?php $page = $page ?? []; ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900"><?php echo $this->escape($title); ?></h1>
</div>

<form method="POST" action="<?php echo $this->escape($action); ?>" class="grid gap-6 lg:grid-cols-3">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">

    <div class="space-y-6 lg:col-span-2">
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <div class="space-y-4">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Title</span>
                    <input name="title" value="<?php echo $this->escape((string) ($page['title'] ?? '')); ?>" required class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Slug</span>
                    <input name="slug" value="<?php echo $this->escape((string) ($page['slug'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Excerpt</span>
                    <textarea name="excerpt" rows="3" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($page['excerpt'] ?? '')); ?></textarea>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Content</span>
                    <textarea name="content" rows="14" required class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($page['content'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>

        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">SEO</h2>
            <div class="mt-4 space-y-4">
                <input name="meta_title" maxlength="255" value="<?php echo $this->escape((string) ($page['meta_title'] ?? '')); ?>" placeholder="SEO title" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                <textarea name="meta_description" rows="3" placeholder="Meta description" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($page['meta_description'] ?? '')); ?></textarea>
                <input name="meta_keywords" value="<?php echo $this->escape((string) ($page['meta_keywords'] ?? '')); ?>" placeholder="Meta keywords" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
            </div>
        </section>
    </div>

    <aside class="space-y-6">
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Publishing</h2>
            <label class="mt-4 flex items-center gap-3 text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_published" value="1" <?php echo !empty($page['is_published']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700 focus:ring-blue-600">
                Published
            </label>
        </section>
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Featured image URL</span>
                <input name="featured_image" value="<?php echo $this->escape((string) ($page['featured_image'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
            </label>
        </section>
        <div class="flex gap-3">
            <button type="submit" class="flex-1 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save</button>
            <a href="/admin/pages" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
        </div>
    </aside>
</form>
