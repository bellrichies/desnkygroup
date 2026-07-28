<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Roles</h1>
        <p class="mt-1 text-sm text-gray-600">Define admin roles and assign permissions.</p>
    </div>
    <a href="/admin/roles/create" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New role</a>
</div>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Slug</th>
                <th class="px-4 py-3">Users</th>
                <th class="px-4 py-3">Type</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($roles as $role) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-950"><?php echo $this->escape((string) $role['name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $this->escape((string) ($role['description'] ?? '')); ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) $role['slug']); ?></td>
                    <td class="px-4 py-3"><?php echo (int) $role['users_count']; ?></td>
                    <td class="px-4 py-3"><?php echo !empty($role['is_system_role']) ? 'System' : 'Custom'; ?></td>
                    <td class="px-4 py-3 text-right"><a href="/admin/roles/<?php echo (int) $role['id']; ?>/edit" class="font-semibold text-blue-700">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
