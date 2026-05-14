<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Services</h1>
        <p class="mt-1 text-sm text-gray-600">Manage service landing pages, icons, categories, ordering, and SEO.</p>
    </div>
    <a href="/admin/services/create" class="inline-flex items-center justify-center rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        Create Service
    </a>
</div>

<div class="overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-4 py-3">Service</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Order</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($services as $service) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900"><?php echo $this->escape((string) $service['title']); ?></p>
                        <p class="text-xs text-gray-500">/services/<?php echo $this->escape((string) $service['slug']); ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($service['category'] ?? '')); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo (int) ($service['sort_order'] ?? 0); ?></td>
                    <td class="px-4 py-3">
                        <span class="rounded-full px-2 py-1 text-xs font-semibold <?php echo !empty($service['is_published']) ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'; ?>">
                            <?php echo !empty($service['is_published']) ? 'Published' : 'Draft'; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="/admin/services/<?php echo (int) $service['id']; ?>/edit" class="rounded border border-gray-300 px-3 py-1.5 font-medium text-gray-700 hover:bg-gray-50">Edit</a>
                            <form method="POST" action="/admin/services/<?php echo (int) $service['id']; ?>/delete" onsubmit="return confirm('Delete this service?');">
                                <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
                                <button class="rounded border border-red-200 px-3 py-1.5 font-medium text-red-700 hover:bg-red-50">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($services)) : ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No services found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
