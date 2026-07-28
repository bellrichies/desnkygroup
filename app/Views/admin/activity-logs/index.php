<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-950">Activity Logs</h1>
    <p class="mt-1 text-sm text-gray-600">Audit trail for admin authentication, CRUD and permission changes.</p>
</div>
<?php echo $this->partial('admin/partials/pagination', ['pagination' => $pagination ?? [], 'filters' => $filters ?? []]); ?>

<form method="get" class="mb-5 grid gap-3 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200 md:grid-cols-3">
    <input name="q" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Search logs">
    <input name="module" value="<?php echo $this->escape((string) ($filters['module'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Module">
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Filter</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">Action</th>
                <th class="px-4 py-3">User</th>
                <th class="px-4 py-3">Module</th>
                <th class="px-4 py-3">IP</th>
                <th class="px-4 py-3">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($activities as $activity) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-950"><?php echo $this->escape((string) $activity['action']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $this->escape((string) ($activity['description'] ?? '')); ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($activity['user_name'] ?? 'System')); ?></td>
                    <td class="px-4 py-3"><?php echo $this->escape((string) $activity['module']); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($activity['ip_address'] ?? '')); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) $activity['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($activities)) : ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No activity logs found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
