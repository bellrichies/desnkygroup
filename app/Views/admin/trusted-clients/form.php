<?php
$client     ??= [];
$services   ??= [];
$csrf_token ??= '';
$action     ??= '/admin/trusted-clients';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/trusted-clients" class="text-sm font-semibold text-blue-700">← Back to clients</a>
</div>

<form method="POST" action="<?php echo $this->escape($action); ?>" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

    <!-- Main -->
    <div class="space-y-6">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Client details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Client name <span class="text-red-600">*</span></span>
                    <input name="name" required value="<?php echo $this->escape((string) ($client['name'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. TotalEnergies Nigeria">
                </label>
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Website URL</span>
                    <input name="website_url" type="url" value="<?php echo $this->escape((string) ($client['website_url'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="https://example.com">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Related service</span>
                    <select name="service_slug" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <option value="">— All services —</option>
                        <?php foreach ($services as $svc) : ?>
                            <option value="<?php echo $this->escape((string) $svc['slug']); ?>" <?php echo ($client['service_slug'] ?? '') === $svc['slug'] ? 'selected' : ''; ?>>
                                <?php echo $this->escape((string) $svc['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="mt-1 block text-xs text-gray-400">Leave blank to show on all service pages.</span>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Sort order</span>
                    <input name="sort_order" type="number" value="<?php echo (int) ($client['sort_order'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
            </div>
        </section>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-5">

        <!-- Publishing -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Publishing</h2>
            <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" <?php echo ($client['is_active'] ?? 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Active
            </label>
            <div class="mt-5 flex gap-3">
                <button class="flex-1 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save</button>
                <a href="/admin/trusted-clients" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </section>

        <!-- Logo -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Logo</h2>
            <p class="mt-1 text-xs text-gray-400">Use a transparent PNG or WebP for best results.</p>
            <div class="mt-4 space-y-3">
                <?php $logo = (string) ($client['logo'] ?? ''); ?>
                <img
                    id="logo_preview"
                    src="<?php echo $this->escape($logo); ?>"
                    alt="Logo preview"
                    class="<?php echo $logo ? '' : 'hidden'; ?> w-full rounded object-contain bg-gray-50 p-2"
                    style="max-height:120px"
                >
                <input type="hidden" id="client_logo" name="logo" value="<?php echo $this->escape($logo); ?>">
                <button
                    type="button"
                    onclick="openMediaPicker('client_logo','logo_preview')"
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <?php echo $logo ? 'Change logo' : 'Select from library'; ?>
                </button>
                <?php if ($logo) : ?>
                    <button type="button" onclick="document.getElementById('client_logo').value='';document.getElementById('logo_preview').classList.add('hidden');" class="w-full rounded border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Remove logo</button>
                <?php endif; ?>
            </div>
        </section>

    </aside>
</form>

<?php echo $this->partial('admin/partials/media-picker'); ?>
