<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Permissions</h1>
        <p class="mt-1 text-sm text-gray-600">Permission catalog grouped by module.</p>
    </div>
    <a href="/admin/permissions/create" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New permission</a>
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
                        <a href="/admin/permissions/<?php echo (int) $permission['id']; ?>/edit" class="font-semibold text-blue-700">Edit</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
