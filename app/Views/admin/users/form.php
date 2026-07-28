<?php $adminUser = $adminUser ?? []; ?>
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/users" class="text-sm font-semibold text-blue-700">Back to users</a>
</div>

<form method="post" action="<?php echo empty($adminUser['id']) ? '/admin/users' : '/admin/users/' . (int) $adminUser['id']; ?>" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="font-semibold text-gray-950">Account</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <input name="full_name" required value="<?php echo $this->escape((string) ($adminUser['full_name'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Full name">
            <input name="email" type="email" required value="<?php echo $this->escape((string) ($adminUser['email'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Email address">
            <?php if (empty($adminUser['id'])) : ?>
                <input name="password" type="text" class="rounded border-gray-300 md:col-span-2" placeholder="Optional password; leave blank to generate one">
            <?php endif; ?>
            <input type="hidden" name="legacy_role" value="<?php echo $this->escape((string) ($adminUser['role'] ?? 'editor')); ?>">
            <label class="flex items-center gap-2 text-sm md:col-span-2">
                <input type="checkbox" name="is_active" <?php echo ($adminUser['is_active'] ?? 1) ? 'checked' : ''; ?>>
                Active account
            </label>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Roles</h2>
            <div class="mt-4 space-y-2">
                <?php foreach ($roles as $role) : ?>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="role_ids[]" value="<?php echo (int) $role['id']; ?>" <?php echo in_array((int) $role['id'], $selectedRoles, true) ? 'checked' : ''; ?>>
                        <?php echo $this->escape((string) $role['name']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button class="mt-5 w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Save user</button>
        </section>
    </aside>
</form>

<?php if (!empty($adminUser['id'])) : ?>
    <div class="mt-6 flex flex-wrap gap-3">
        <form method="post" action="/admin/users/<?php echo (int) $adminUser['id']; ?>/reset-password">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
            <button class="rounded border border-amber-300 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-800">Reset password</button>
        </form>
        <form method="post" action="/admin/users/<?php echo (int) $adminUser['id']; ?>/delete" onsubmit="return confirm('Delete this admin user?')">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
            <button class="rounded border border-red-300 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700">Delete user</button>
        </form>
    </div>
<?php endif; ?>
