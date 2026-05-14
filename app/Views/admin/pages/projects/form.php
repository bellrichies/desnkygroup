<?php $project = $project ?? []; ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900"><?php echo $this->escape($title); ?></h1>
</div>

<form method="POST" action="<?php echo $this->escape($action); ?>" class="grid gap-6 lg:grid-cols-3">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
    <div class="space-y-6 lg:col-span-2">
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2"><span class="text-sm font-semibold text-gray-700">Title</span><input name="title" value="<?php echo $this->escape((string) ($project['title'] ?? '')); ?>" required class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="block"><span class="text-sm font-semibold text-gray-700">Slug</span><input name="slug" value="<?php echo $this->escape((string) ($project['slug'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="block"><span class="text-sm font-semibold text-gray-700">Category</span><input name="category" value="<?php echo $this->escape((string) ($project['category'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="block"><span class="text-sm font-semibold text-gray-700">Client</span><input name="client_name" value="<?php echo $this->escape((string) ($project['client_name'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="block"><span class="text-sm font-semibold text-gray-700">Project date</span><input type="date" name="project_date" value="<?php echo $this->escape((string) ($project['project_date'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="block"><span class="text-sm font-semibold text-gray-700">Sort order</span><input type="number" name="sort_order" value="<?php echo (int) ($project['sort_order'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
                <label class="block sm:col-span-2"><span class="text-sm font-semibold text-gray-700">Summary</span><textarea name="summary" rows="3" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($project['summary'] ?? '')); ?></textarea></label>
                <label class="block sm:col-span-2"><span class="text-sm font-semibold text-gray-700">Description</span><textarea name="description" rows="12" class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($project['description'] ?? '')); ?></textarea></label>
            </div>
        </section>
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">SEO</h2>
            <div class="mt-4 grid gap-4">
                <input name="meta_title" value="<?php echo $this->escape((string) ($project['meta_title'] ?? '')); ?>" placeholder="SEO title" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                <textarea name="meta_description" rows="3" placeholder="Meta description" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($project['meta_description'] ?? '')); ?></textarea>
                <input name="meta_keywords" value="<?php echo $this->escape((string) ($project['meta_keywords'] ?? '')); ?>" placeholder="Meta keywords" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                <input name="canonical_url" value="<?php echo $this->escape((string) ($project['canonical_url'] ?? '')); ?>" placeholder="Canonical URL" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                <input name="og_image" value="<?php echo $this->escape((string) ($project['og_image'] ?? '')); ?>" placeholder="Open Graph image URL" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
            </div>
        </section>
    </div>
    <aside class="space-y-6">
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <label class="flex items-center gap-3 text-sm font-medium text-gray-700"><input type="checkbox" name="is_published" value="1" <?php echo !empty($project['is_published']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700 focus:ring-blue-600">Published</label>
        </section>
        <section class="rounded border border-gray-200 bg-white p-5 shadow-sm">
            <label class="block"><span class="text-sm font-semibold text-gray-700">Featured image URL</span><input name="featured_image" value="<?php echo $this->escape((string) ($project['featured_image'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"></label>
        </section>
        <div class="flex gap-3"><button class="flex-1 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save</button><a href="/admin/projects" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a></div>
    </aside>
</form>
