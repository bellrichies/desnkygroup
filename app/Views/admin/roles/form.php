<?php $role = $role ?? []; ?>
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/roles" class="text-sm font-semibold text-blue-700">Back to roles</a>
</div>

<form method="post" action="<?php echo empty($role['id']) ? '/admin/roles' : '/admin/roles/' . (int) $role['id']; ?>" class="space-y-6">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="font-semibold text-gray-950">Role details</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <input name="name" required value="<?php echo $this->escape((string) ($role['name'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Role name">
            <input name="slug" value="<?php echo $this->escape((string) ($role['slug'] ?? '')); ?>" class="rounded border-gray-300" placeholder="role-slug">
            <input name="sort_order" type="number" value="<?php echo $this->escape((string) ($role['sort_order'] ?? 0)); ?>" class="rounded border-gray-300" placeholder="Sort order">
            <textarea name="description" class="rounded border-gray-300 md:col-span-3" placeholder="Description"><?php echo $this->escape((string) ($role['description'] ?? '')); ?></textarea>
        </div>
    </section>

    <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="font-semibold text-gray-950">Permissions</h2>
        <div class="mt-4 grid gap-5 lg:grid-cols-2">
            <?php foreach ($permissions as $module => $modulePermissions) : ?>
                <div class="rounded border border-gray-200 p-4">
                    <h3 class="font-semibold capitalize text-gray-950"><?php echo $this->escape((string) $module); ?></h3>
                    <div class="mt-3 grid gap-2">
                        <?php foreach ($modulePermissions as $permission) : ?>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="permission_ids[]" value="<?php echo (int) $permission['id']; ?>" <?php echo in_array((int) $permission['id'], $selectedPermissions, true) ? 'checked' : ''; ?>>
                                <?php echo $this->escape((string) $permission['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="mt-5 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Save role</button>
    </section>
</form>

<?php if (!empty($role['id']) && empty($role['is_system_role'])) : ?>
    <form method="post" action="/admin/roles/<?php echo (int) $role['id']; ?>/delete" class="mt-4" onsubmit="return confirm('Delete this role?')">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
        <button class="rounded border border-red-300 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700">Delete role</button>
    </form>
<?php endif; ?>
