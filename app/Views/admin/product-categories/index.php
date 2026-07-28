<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Product Categories</h1>
        <p class="mt-1 text-sm text-gray-600">Create nested storefront categories with SEO metadata.</p>
    </div>
    <a href="/admin/product-categories/create" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New category</a>
</div>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Parent</th>
                <th class="px-4 py-3">Slug</th>
                <th class="px-4 py-3">Sort</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($categories as $category) : ?>
                <tr>
                    <td class="px-4 py-3 font-semibold text-gray-950"><?php echo $this->escape((string) $category['name']); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($category['parent_name'] ?? 'Root')); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) $category['slug']); ?></td>
                    <td class="px-4 py-3"><?php echo (int) ($category['sort_order'] ?? 0); ?></td>
                    <td class="px-4 py-3"><?php echo !empty($category['is_active']) ? 'Active' : 'Inactive'; ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="/admin/product-categories/<?php echo (int) $category['id']; ?>/edit" class="font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)) : ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No categories found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
