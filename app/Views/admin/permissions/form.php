<?php $permission = $permission ?? []; ?>
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/permissions" class="text-sm font-semibold text-blue-700">Back to permissions</a>
</div>

<form method="post" action="<?php echo empty($permission['id']) ? '/admin/permissions' : '/admin/permissions/' . (int) $permission['id']; ?>" class="max-w-3xl rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    <div class="grid gap-4 md:grid-cols-2">
        <input name="name" required value="<?php echo $this->escape((string) ($permission['name'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Permission name">
        <input name="slug" required value="<?php echo $this->escape((string) ($permission['slug'] ?? '')); ?>" class="rounded border-gray-300" placeholder="module.action">
        <input name="module" required value="<?php echo $this->escape((string) ($permission['module'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Module">
        <select name="parent_id" class="rounded border-gray-300">
            <option value="">No parent</option>
            <?php foreach ($permissions as $option) : ?>
                <?php if (($permission['id'] ?? null) === $option['id']) { continue; } ?>
                <option value="<?php echo (int) $option['id']; ?>" <?php echo (string) ($permission['parent_id'] ?? '') === (string) $option['id'] ? 'selected' : ''; ?>>
                    <?php echo $this->escape((string) $option['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <textarea name="description" class="rounded border-gray-300 md:col-span-2" placeholder="Description"><?php echo $this->escape((string) ($permission['description'] ?? '')); ?></textarea>
    </div>
    <button class="mt-5 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Save permission</button>
</form>
