<?php
$categories = isset($categories) && is_array($categories) ? $categories : [];
$tags = isset($tags) && is_array($tags) ? $tags : [];
$csrf = (string) ($csrf_token ?? '');
$activeTab = (string) (($filters['tab'] ?? 'categories') === 'tags' ? 'tags' : 'categories');
$records = $activeTab === 'tags' ? $tags : $categories;
$isCategories = $activeTab === 'categories';
?>

<div x-data="{ tab: '<?php echo $activeTab; ?>', modal: null }" @open-taxonomy.window="modal = 'edit'" class="space-y-4">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-950">Blog taxonomy</h1>
            <p class="mt-0.5 text-xs text-slate-500">Organise content discovery with categories and tags.</p>
        </div>
        <div class="flex gap-2">
            <a href="/admin/blog" class="inline-flex h-10 min-w-28 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm">Back to posts</a>
            <button type="button" @click="modal = 'create'" onclick="resetTaxonomyCreate()" class="inline-flex h-10 min-w-32 items-center justify-center rounded-lg bg-blue-700 px-4 text-sm font-bold !text-white shadow-sm">+ Add <?php echo $isCategories ? 'category' : 'tag'; ?></button>
        </div>
    </header>

    <nav class="inline-flex rounded-xl border border-slate-200 bg-white p-1 shadow-sm" aria-label="Taxonomy tabs">
        <a href="/admin/blog/categories?tab=categories" class="inline-flex h-9 items-center rounded-lg px-5 text-sm font-semibold <?php echo $isCategories ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'; ?>">Categories</a>
        <a href="/admin/blog/categories?tab=tags" class="inline-flex h-9 items-center rounded-lg px-5 text-sm font-semibold <?php echo !$isCategories ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-500 hover:bg-slate-50'; ?>">Tags</a>
    </nav>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
            <div><h2 class="text-sm font-bold text-slate-950"><?php echo $isCategories ? 'Categories' : 'Tags'; ?></h2><p class="text-[11px] text-slate-400">Showing up to 10 records per page</p></div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-4 py-2.5">Name</th><th class="px-4 py-2.5">Slug</th><th class="px-4 py-2.5">Posts</th><?php if ($isCategories) : ?><th class="px-4 py-2.5">Status</th><th class="px-4 py-2.5">Order</th><?php endif; ?><th class="px-4 py-2.5 text-right">Actions</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($records as $record) : ?>
                        <?php $payload = htmlspecialchars(json_encode($record, JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}', ENT_QUOTES, 'UTF-8'); ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-2.5"><p class="font-semibold text-slate-900"><?php echo $this->escape((string) $record['name']); ?></p><p class="max-w-xs truncate text-[11px] text-slate-400"><?php echo $this->escape((string) ($record['description'] ?? 'No description')); ?></p></td>
                            <td class="px-4 py-2.5 font-mono text-xs text-slate-500"><?php echo $this->escape((string) $record['slug']); ?></td>
                            <td class="px-4 py-2.5 text-slate-600"><?php echo (int) ($record['post_count'] ?? 0); ?></td>
                            <?php if ($isCategories) : ?>
                                <td class="px-4 py-2.5">
                                    <form method="post" action="/admin/blog/categories/<?php echo (int) $record['id']; ?>">
                                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>"><input type="hidden" name="name" value="<?php echo $this->escape((string) $record['name']); ?>"><input type="hidden" name="slug" value="<?php echo $this->escape((string) $record['slug']); ?>"><input type="hidden" name="description" value="<?php echo $this->escape((string) ($record['description'] ?? '')); ?>"><input type="hidden" name="seo_title" value="<?php echo $this->escape((string) ($record['seo_title'] ?? '')); ?>"><input type="hidden" name="meta_description" value="<?php echo $this->escape((string) ($record['meta_description'] ?? '')); ?>"><input type="hidden" name="sort_order" value="<?php echo (int) ($record['sort_order'] ?? 0); ?>"><input type="hidden" name="is_active" value="<?php echo empty($record['is_active']) ? '1' : '0'; ?>">
                                        <button class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold <?php echo !empty($record['is_active']) ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'; ?>"><?php echo !empty($record['is_active']) ? 'Active' : 'Inactive'; ?></button>
                                    </form>
                                </td>
                                <td class="px-4 py-2.5 text-slate-500"><?php echo (int) ($record['sort_order'] ?? 0); ?></td>
                            <?php endif; ?>
                            <td class="px-4 py-2.5">
                                <div class="flex justify-end gap-3">
                                    <button type="button" data-record="<?php echo $payload; ?>" onclick="openTaxonomyEditor(this)" class="text-xs font-semibold text-blue-700">Edit</button>
                                    <form method="post" action="/admin/blog/<?php echo $isCategories ? 'categories' : 'tags'; ?>/<?php echo (int) $record['id']; ?>/delete" onsubmit="return confirm('Delete this record?')"><input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>"><button class="text-xs font-semibold text-red-600">Delete</button></form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($records === []) : ?><tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">No records found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php echo $this->partial('admin/partials/pagination', ['pagination' => $pagination ?? [], 'filters' => ['tab' => $activeTab]]); ?>

    <div x-show="modal" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/55 p-4" @keydown.escape.window="modal = null">
        <div class="w-full max-w-xl rounded-2xl bg-white shadow-2xl" @click.outside="modal = null">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><h2 id="taxonomy-modal-title" class="font-bold text-slate-950">Add <?php echo $isCategories ? 'category' : 'tag'; ?></h2><button type="button" @click="modal = null" class="text-slate-400">✕</button></div>
            <form id="taxonomy-form" method="post" action="/admin/blog/<?php echo $isCategories ? 'categories' : 'tags'; ?>" class="grid gap-3 p-5">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                <div class="grid gap-3 sm:grid-cols-2"><label><span class="text-xs font-bold text-slate-600">Name</span><input id="tax-name" name="name" required class="mt-1 h-10 w-full rounded-lg border-slate-300"></label><label><span class="text-xs font-bold text-slate-600">Slug</span><input id="tax-slug" name="slug" class="mt-1 h-10 w-full rounded-lg border-slate-300 font-mono text-sm"></label></div>
                <label><span class="text-xs font-bold text-slate-600">Description</span><textarea id="tax-description" name="description" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea></label>
                <?php if ($isCategories) : ?>
                    <div class="grid gap-3 sm:grid-cols-2"><label><span class="text-xs font-bold text-slate-600">SEO title</span><input id="tax-seo-title" name="seo_title" class="mt-1 h-10 w-full rounded-lg border-slate-300"></label><label><span class="text-xs font-bold text-slate-600">Meta description</span><input id="tax-meta" name="meta_description" class="mt-1 h-10 w-full rounded-lg border-slate-300"></label></div>
                    <div class="flex items-center gap-6"><label class="flex items-center gap-2 text-sm"><input id="tax-active" type="checkbox" name="is_active" value="1" checked> Active</label><label class="flex items-center gap-2 text-sm">Order <input id="tax-order" type="number" name="sort_order" value="0" class="h-9 w-20 rounded-lg border-slate-300"></label></div>
                <?php endif; ?>
                <div class="mt-2 flex justify-end gap-2"><button type="button" @click="modal = null" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600">Cancel</button><button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Save record</button></div>
            </form>
        </div>
    </div>
</div>
<script>
function resetTaxonomyCreate() {
    const form = document.getElementById('taxonomy-form');
    form.reset(); form.action = '/admin/blog/<?php echo $isCategories ? 'categories' : 'tags'; ?>';
    document.getElementById('taxonomy-modal-title').textContent = 'Add <?php echo $isCategories ? 'category' : 'tag'; ?>';
}
function openTaxonomyEditor(button) {
    const record = JSON.parse(button.dataset.record);
    const form = document.getElementById('taxonomy-form');
    form.action = '/admin/blog/<?php echo $isCategories ? 'categories' : 'tags'; ?>/' + record.id;
    document.getElementById('taxonomy-modal-title').textContent = 'Edit <?php echo $isCategories ? 'category' : 'tag'; ?>';
    document.getElementById('tax-name').value = record.name || '';
    document.getElementById('tax-slug').value = record.slug || '';
    document.getElementById('tax-description').value = record.description || '';
    <?php if ($isCategories) : ?>document.getElementById('tax-seo-title').value = record.seo_title || ''; document.getElementById('tax-meta').value = record.meta_description || ''; document.getElementById('tax-active').checked = Number(record.is_active) === 1; document.getElementById('tax-order').value = record.sort_order || 0;<?php endif; ?>
    window.dispatchEvent(new CustomEvent('open-taxonomy'));
}
</script>
