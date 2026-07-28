<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Permissions</h1>
        <p class="mt-1 text-sm text-gray-600">Permission catalog grouped by module.</p>
    </div>
    <button type="button" onclick="openPermissionModal()" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New permission</button>
</div>

<div class="grid gap-5 lg:grid-cols-2">
    <?php foreach ($groupedPermissions as $module => $permissions) : ?>
        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold capitalize text-gray-950"><?php echo $this->escape((string) $module); ?></h2>
            <div class="mt-4 divide-y divide-gray-100">
                <?php foreach ($permissions as $permission) : ?>
                    <div class="flex items-center justify-between gap-4 py-3 text-sm">
                        <div>
                            <p class="font-medium text-gray-950"><?php echo $this->escape((string) $permission['name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo $this->escape((string) $permission['slug']); ?></p>
                        </div>
                        <button type="button" onclick='openPermissionModal(<?php echo $this->escapeJson($permission); ?>)' class="font-semibold text-blue-700">Edit</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<dialog id="permission-modal" class="w-full max-w-2xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/60">
    <form id="permission-form" method="post" action="/admin/permissions" class="p-6">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
        <div class="flex items-center justify-between"><h2 id="permission-title" class="text-xl font-bold">Add permission</h2><button type="button" onclick="this.closest('dialog').close()" class="text-2xl" aria-label="Close">&times;</button></div>
        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <label><span class="text-sm font-semibold">Name</span><input id="permission-name" name="name" required class="mt-1 w-full border-gray-300"></label>
            <label><span class="text-sm font-semibold">Slug</span><input id="permission-slug" name="slug" required placeholder="module.action" class="mt-1 w-full border-gray-300"></label>
            <label><span class="text-sm font-semibold">Module</span><input id="permission-module" name="module" required class="mt-1 w-full border-gray-300"></label>
            <label><span class="text-sm font-semibold">Parent</span><select id="permission-parent" name="parent_id" class="mt-1 w-full border-gray-300"><option value="">No parent</option><?php foreach (($permissions ?? []) as $option) : ?><option value="<?php echo (int) $option['id']; ?>"><?php echo $this->escape((string) $option['name']); ?></option><?php endforeach; ?></select></label>
            <label class="md:col-span-2"><span class="text-sm font-semibold">Description</span><textarea id="permission-description" name="description" rows="3" class="mt-1 w-full border-gray-300"></textarea></label>
        </div>
        <div class="mt-6 flex justify-end gap-3"><button type="button" onclick="this.closest('dialog').close()" class="rounded-lg border px-4 py-2">Cancel</button><button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Save permission</button></div>
    </form>
</dialog>
<script>
function openPermissionModal(permission) {
    permission = permission || {};
    const form = document.getElementById('permission-form');
    form.action = permission.id ? '/admin/permissions/' + permission.id : '/admin/permissions';
    document.getElementById('permission-title').textContent = permission.id ? 'Edit permission' : 'Add permission';
    document.getElementById('permission-name').value = permission.name || '';
    document.getElementById('permission-slug').value = permission.slug || '';
    document.getElementById('permission-module').value = permission.module || '';
    document.getElementById('permission-parent').value = permission.parent_id || '';
    document.getElementById('permission-description').value = permission.description || '';
    document.getElementById('permission-modal').showModal();
}
</script>
