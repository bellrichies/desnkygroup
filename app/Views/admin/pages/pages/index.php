<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pages</h1>
        <p class="mt-1 text-sm text-gray-600">Manage website pages, drafts, featured images, and SEO metadata.</p>
    </div>
    <a href="/admin/pages/create" class="inline-flex items-center justify-center rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        Create Page
    </a>
</div>

<div class="overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Updated</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($pages as $page) : ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-900"><?php echo $this->escape((string) $page['title']); ?></td>
                        <td class="px-4 py-3 text-gray-600">/<?php echo $this->escape((string) $page['slug']); ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo !empty($page['is_published']) ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
                                <?php echo !empty($page['is_published']) ? 'Published' : 'Draft'; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($page['updated_at'] ?? $page['created_at'] ?? '')); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="/admin/pages/<?php echo (int) $page['id']; ?>/edit" class="rounded border border-gray-300 px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="/admin/pages/<?php echo (int) $page['id']; ?>/delete" onsubmit="return confirm('Delete this page?');">
                                    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
                                    <button class="rounded border border-red-200 px-3 py-1.5 font-medium text-red-700 hover:bg-red-50" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pages)) : ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No pages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
