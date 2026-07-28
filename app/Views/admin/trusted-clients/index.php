<?php
$clients    ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Trusted Clients</h1>
        <p class="mt-1 text-sm text-gray-500">Logos shown on the home page, service pages, and about section.</p>
    </div>
    <button type="button" onclick="openClientModal()" class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        + Add client
    </button>
</div>

<?php if (empty($clients)) : ?>
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-sm text-gray-400">
        No trusted clients added yet. <a href="/admin/trusted-clients/create" class="font-semibold text-blue-700 hover:underline">Add one now.</a>
    </div>
<?php else : ?>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Logo</th>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Service</th>
                    <th class="px-5 py-3 text-left">Website</th>
                    <th class="px-5 py-3 text-left">Order</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($clients as $client) : ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <?php if (!empty($client['logo'])) : ?>
                                <img src="<?php echo $this->escape((string) $client['logo']); ?>" alt="<?php echo $this->escape((string) $client['name']); ?>" class="h-10 w-16 rounded object-contain">
                            <?php else : ?>
                                <span class="inline-flex h-10 w-16 items-center justify-center rounded bg-gray-100 text-xs text-gray-400">No logo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-900"><?php echo $this->escape((string) $client['name']); ?></td>
                        <td class="px-5 py-3 text-gray-500"><?php echo $this->escape((string) ($client['service_slug'] ?: '— All services —')); ?></td>
                        <td class="px-5 py-3 text-gray-500">
                            <?php if (!empty($client['website_url'])) : ?>
                                <a href="<?php echo $this->escape((string) $client['website_url']); ?>" target="_blank" rel="noopener" class="text-blue-700 hover:underline">Visit ↗</a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-gray-500"><?php echo (int) $client['sort_order']; ?></td>
                        <td class="px-5 py-3">
                            <form method="POST" action="/admin/trusted-clients/<?php echo (int) $client['id']; ?>/toggle">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $client['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'; ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?php echo $client['is_active'] ? 'bg-green-500' : 'bg-gray-400'; ?>"></span>
                                    <?php echo $client['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <button type="button" onclick='openClientModal(<?php echo $this->escapeJson($client); ?>)' class="font-semibold text-blue-700 hover:underline">Edit</button>
                            <form method="POST" action="/admin/trusted-clients/<?php echo (int) $client['id']; ?>/delete" class="inline" onsubmit="return confirm('Delete this client?');">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="ml-3 font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php echo $this->partial('admin/partials/pagination', ['pagination' => $pagination ?? [], 'filters' => $filters ?? []]); ?>

<dialog id="client-modal" class="w-full max-w-2xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/60">
    <form id="client-modal-form" method="post" action="/admin/trusted-clients" class="p-6">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
        <div class="flex items-center justify-between"><h2 id="client-modal-title" class="text-xl font-bold">Add client</h2><button type="button" onclick="this.closest('dialog').close()" class="text-2xl" aria-label="Close">&times;</button></div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <label class="sm:col-span-2"><span class="text-sm font-semibold">Client name</span><input id="client-name" name="name" required class="mt-1 w-full border-gray-300"></label>
            <label class="sm:col-span-2"><span class="text-sm font-semibold">Website URL</span><input id="client-website" name="website_url" type="url" class="mt-1 w-full border-gray-300"></label>
            <label><span class="text-sm font-semibold">Related service</span><select id="client-service" name="service_slug" class="mt-1 w-full border-gray-300"><option value="">All services</option><?php foreach (($services ?? []) as $service) : ?><option value="<?php echo $this->escape((string) $service['slug']); ?>"><?php echo $this->escape((string) $service['title']); ?></option><?php endforeach; ?></select></label>
            <label><span class="text-sm font-semibold">Sort order</span><input id="client-sort" name="sort_order" type="number" value="0" class="mt-1 w-full border-gray-300"></label>
            <label class="flex items-center gap-2"><input id="client-active" name="is_active" type="checkbox" value="1" checked> Active</label>
            <div class="sm:col-span-2 rounded-xl border border-dashed p-4"><input id="client-logo" name="logo" type="hidden"><img id="client-logo-preview" alt="" class="mb-3 hidden h-20 max-w-full object-contain"><button type="button" onclick="openClientLogoPicker()" class="rounded-lg border px-3 py-2 text-sm font-semibold">Select logo from library</button></div>
        </div>
        <div class="mt-6 flex justify-end gap-3"><button type="button" onclick="this.closest('dialog').close()" class="rounded-lg border px-4 py-2">Cancel</button><button class="rounded-lg bg-blue-700 px-4 py-2 font-semibold text-white">Save client</button></div>
    </form>
</dialog>
<script>
let restoreClientDialogAfterMedia = false;

function openClientModal(client) {
    client = client || {};
    const dialog = document.getElementById('client-modal');
    const form = document.getElementById('client-modal-form');
    form.action = client.id ? '/admin/trusted-clients/' + client.id : '/admin/trusted-clients';
    document.getElementById('client-modal-title').textContent = client.id ? 'Edit client' : 'Add client';
    document.getElementById('client-name').value = client.name || '';
    document.getElementById('client-website').value = client.website_url || '';
    document.getElementById('client-service').value = client.service_slug || '';
    document.getElementById('client-sort').value = client.sort_order || 0;
    document.getElementById('client-active').checked = client.id ? Boolean(Number(client.is_active)) : true;
    document.getElementById('client-logo').value = client.logo || '';
    const preview = document.getElementById('client-logo-preview');
    preview.src = client.logo || ''; preview.classList.toggle('hidden', !client.logo);
    dialog.showModal();
}

function openClientLogoPicker() {
    const dialog = document.getElementById('client-modal');
    restoreClientDialogAfterMedia = dialog.open;

    if (dialog.open) {
        dialog.close();
    }

    window.requestAnimationFrame(() => {
        openMediaPicker('client-logo', 'client-logo-preview');
    });
}

window.addEventListener('media-picker:closed', () => {
    if (!restoreClientDialogAfterMedia) {
        return;
    }

    restoreClientDialogAfterMedia = false;
    const dialog = document.getElementById('client-modal');
    if (!dialog.open) {
        dialog.showModal();
    }
});
</script>
<?php echo $this->partial('admin/partials/media-picker'); ?>
