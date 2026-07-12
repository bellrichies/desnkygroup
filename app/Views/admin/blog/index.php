<?php
$posts = $posts ?? [];
$categories = $categories ?? [];
$filters = $filters ?? [];
$statuses = $statuses ?? ['draft', 'scheduled', 'published', 'archived'];
$counts = $stats['counts'] ?? [];
$csrf = (string) ($csrf_token ?? '');
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Blog Posts</h1>
        <p class="mt-1 text-sm text-gray-600">Create, schedule, publish, archive, and track editorial content.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="/admin/blog/categories" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Taxonomy</a>
        <a href="/admin/blog/authors" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Authors</a>
        <a href="/admin/blog/ads" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Ads</a>
        <a href="/admin/blog/create" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New post</a>
    </div>
</div>

<div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <?php foreach (['total' => 'Total', 'published' => 'Published', 'draft' => 'Drafts', 'scheduled' => 'Scheduled', 'archived' => 'Archived'] as $key => $label) : ?>
        <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500"><?php echo $label; ?></p>
            <p class="mt-2 text-2xl font-bold text-gray-950"><?php echo (int) ($counts[$key] ?? 0); ?></p>
        </div>
    <?php endforeach; ?>
</div>

<form method="get" class="mb-5 grid gap-3 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200 md:grid-cols-5">
    <input name="q" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Search title or content">
    <select name="category_id" class="rounded border-gray-300">
        <option value="">All categories</option>
        <?php foreach ($categories as $category) : ?>
            <option value="<?php echo (int) $category['id']; ?>" <?php echo (string) ($filters['category_id'] ?? '') === (string) $category['id'] ? 'selected' : ''; ?>>
                <?php echo $this->escape((string) $category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="status" class="rounded border-gray-300">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $status) : ?>
            <option value="<?php echo $this->escape($status); ?>" <?php echo ($filters['status'] ?? '') === $status ? 'selected' : ''; ?>>
                <?php echo ucfirst($status); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <label class="inline-flex items-center gap-2 rounded border border-gray-200 px-3 py-2 text-sm text-gray-700">
        <input type="checkbox" name="include_deleted" value="1" <?php echo !empty($filters['include_deleted']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
        Include deleted
    </label>
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Filter</button>
</form>

<div class="overflow-hidden rounded bg-white shadow-sm ring-1 ring-gray-200">
    <form id="blog-bulk-status" method="post" action="/admin/blog/bulk-status" class="flex flex-col gap-3 border-b border-gray-200 p-4 sm:flex-row sm:items-center">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
        <select name="status" class="rounded border-gray-300 text-sm">
            <?php foreach ($statuses as $status) : ?>
                <option value="<?php echo $this->escape($status); ?>"><?php echo ucfirst($status); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Apply to selected</button>
        <p class="text-sm text-gray-500">Bulk updates are audited and never affect deleted posts.</p>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
                <tr>
                    <th class="w-10 px-4 py-3"><span class="sr-only">Select</span></th>
                    <th class="px-4 py-3">Post</th>
                    <th class="px-4 py-3">Author</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Views</th>
                    <th class="px-4 py-3">Publish date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($posts as $post) : ?>
                    <?php $deleted = !empty($post['deleted_at']); ?>
                    <tr class="<?php echo $deleted ? 'bg-red-50/50' : ''; ?>">
                        <td class="px-4 py-3">
                            <?php if (!$deleted) : ?>
                                <input form="blog-bulk-status" type="checkbox" name="ids[]" value="<?php echo (int) $post['id']; ?>" class="rounded border-gray-300 text-blue-700">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-950"><?php echo $this->escape((string) $post['title']); ?></p>
                            <p class="text-xs text-gray-500">/<?php echo $this->escape((string) $post['slug']); ?></p>
                            <?php if (!empty($post['is_featured'])) : ?>
                                <span class="mt-1 inline-flex rounded bg-purple-50 px-2 py-0.5 text-xs font-semibold text-purple-700">Featured</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($post['author_name'] ?? 'Unassigned')); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($post['category_name'] ?? 'Uncategorized')); ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700"><?php echo $this->escape((string) $post['status']); ?></span>
                        </td>
                        <td class="px-4 py-3 text-gray-700"><?php echo number_format((int) $post['view_count']); ?></td>
                        <td class="px-4 py-3 text-gray-600">
                            <?php echo !empty($post['publish_date']) ? $this->escape(date('M j, Y', strtotime((string) $post['publish_date']))) : '-'; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <?php if ($deleted) : ?>
                                    <form method="post" action="/admin/blog/<?php echo (int) $post['id']; ?>/restore">
                                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                                        <button class="font-semibold text-green-700 hover:text-green-900">Restore</button>
                                    </form>
                                <?php else : ?>
                                    <a href="/admin/blog/<?php echo (int) $post['id']; ?>/preview" target="_blank" class="font-semibold text-gray-700 hover:text-gray-950">Preview</a>
                                    <a href="/admin/blog/<?php echo (int) $post['id']; ?>/edit" class="font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($posts === []) : ?>
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No blog posts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
