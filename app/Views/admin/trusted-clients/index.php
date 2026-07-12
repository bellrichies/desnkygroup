<?php
$clients    ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Trusted Clients</h1>
        <p class="mt-1 text-sm text-gray-500">Logos shown on the home page, service pages, and about section.</p>
    </div>
    <a href="/admin/trusted-clients/create" class="inline-flex items-center gap-2 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        + Add client
    </a>
</div>

<?php if (empty($clients)) : ?>
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-sm text-gray-400">
        No trusted clients added yet. <a href="/admin/trusted-clients/create" class="font-semibold text-blue-700 hover:underline">Add one now.</a>
    </div>
<?php else : ?>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Logo</th>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Service</th>
                    <th class="px-5 py-3 text-left">Website</th>
                    <th class="px-5 py-3 text-left">Order</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($clients as $client) : ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <?php if (!empty($client['logo'])) : ?>
                                <img src="<?php echo $this->escape((string) $client['logo']); ?>" alt="<?php echo $this->escape((string) $client['name']); ?>" class="h-10 w-16 rounded object-contain">
                            <?php else : ?>
                                <span class="inline-flex h-10 w-16 items-center justify-center rounded bg-gray-100 text-xs text-gray-400">No logo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-900"><?php echo $this->escape((string) $client['name']); ?></td>
                        <td class="px-5 py-3 text-gray-500"><?php echo $this->escape((string) ($client['service_slug'] ?: '— All services —')); ?></td>
                        <td class="px-5 py-3 text-gray-500">
                            <?php if (!empty($client['website_url'])) : ?>
                                <a href="<?php echo $this->escape((string) $client['website_url']); ?>" target="_blank" rel="noopener" class="text-blue-700 hover:underline">Visit ↗</a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-gray-500"><?php echo (int) $client['sort_order']; ?></td>
                        <td class="px-5 py-3">
                            <form method="POST" action="/admin/trusted-clients/<?php echo (int) $client['id']; ?>/toggle">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $client['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'; ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?php echo $client['is_active'] ? 'bg-green-500' : 'bg-gray-400'; ?>"></span>
                                    <?php echo $client['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="/admin/trusted-clients/<?php echo (int) $client['id']; ?>/edit" class="font-semibold text-blue-700 hover:underline">Edit</a>
                            <form method="POST" action="/admin/trusted-clients/<?php echo (int) $client['id']; ?>/delete" class="inline" onsubmit="return confirm('Delete this client?');">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="ml-3 font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
