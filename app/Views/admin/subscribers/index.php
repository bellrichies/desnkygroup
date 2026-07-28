<?php
$subscribers ??= [];
$filters     ??= [];
$csrf_token  ??= '';
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Newsletter Subscribers</h1>
        <p class="mt-1 text-sm text-gray-500"><?php echo count($subscribers); ?> subscriber<?php echo count($subscribers) !== 1 ? 's' : ''; ?> found.</p>
    </div>
    <a href="/admin/subscribers/export" class="inline-flex items-center gap-2 rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
        ↓ Export CSV
    </a>
</div>

<!-- Filters -->
<form method="GET" action="/admin/subscribers" class="mb-5 flex flex-wrap gap-3">
    <input name="q" type="search" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" placeholder="Search email or name…" class="rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
    <select name="status" class="rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
        <option value="">All statuses</option>
        <option value="subscribed" <?php echo ($filters['status'] ?? '') === 'subscribed' ? 'selected' : ''; ?>>Subscribed</option>
        <option value="unsubscribed" <?php echo ($filters['status'] ?? '') === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
    </select>
    <button class="rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Filter</button>
    <?php if (!empty($filters['q']) || !empty($filters['status'])) : ?>
        <a href="/admin/subscribers" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($subscribers)) : ?>
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-sm text-gray-400">No subscribers match your filter.</div>
<?php else : ?>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Email</th>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Source</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-left">Subscribed</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($subscribers as $sub) : ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold text-gray-900"><?php echo $this->escape((string) $sub['email']); ?></td>
                        <td class="px-5 py-3 text-gray-500"><?php echo $this->escape((string) ($sub['name'] ?? '—')); ?></td>
                        <td class="px-5 py-3 text-gray-400"><?php echo $this->escape((string) ($sub['source'] ?? 'homepage')); ?></td>
                        <td class="px-5 py-3">
                            <?php if (($sub['status'] ?? 'subscribed') === 'subscribed') : ?>
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800">Subscribed</span>
                            <?php else : ?>
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">Unsubscribed</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-gray-400 whitespace-nowrap"><?php echo $this->escape(substr((string) ($sub['created_at'] ?? ''), 0, 10)); ?></td>
                        <td class="px-5 py-3 text-right flex items-center justify-end gap-3">
                            <?php if (($sub['status'] ?? 'subscribed') === 'subscribed') : ?>
                                <form method="POST" action="/admin/subscribers/<?php echo (int) $sub['id']; ?>/unsubscribe">
                                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                    <button class="font-semibold text-amber-600 hover:underline">Unsubscribe</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" action="/admin/subscribers/<?php echo (int) $sub['id']; ?>/delete" onsubmit="return confirm('Delete this subscriber?');">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
