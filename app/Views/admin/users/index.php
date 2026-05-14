<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Admin Users</h1>
        <p class="mt-1 text-sm text-gray-600">Manage admin accounts, role assignments and access status.</p>
    </div>
    <div class="flex gap-2">
        <a href="/admin/users/export?<?php echo http_build_query($filters); ?>" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Export</a>
        <a href="/admin/users/create" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New user</a>
    </div>
</div>

<form method="get" class="mb-5 flex gap-3 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
    <input name="q" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" class="w-full rounded border-gray-300" placeholder="Search users">
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Search</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">User</th>
                <th class="px-4 py-3">Roles</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Last login</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($users as $adminUser) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-950"><?php echo $this->escape((string) $adminUser['full_name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $this->escape((string) $adminUser['email']); ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($adminUser['role_names'] ?? 'No roles')); ?></td>
                    <td class="px-4 py-3"><?php echo !empty($adminUser['is_active']) ? 'Active' : 'Suspended'; ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($adminUser['last_login_at'] ?? 'Never')); ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="/admin/users/<?php echo (int) $adminUser['id']; ?>/edit" class="font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users)) : ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No admin users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
