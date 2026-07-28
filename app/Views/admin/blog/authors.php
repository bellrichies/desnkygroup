<?php
$authors = isset($authors) && is_array($authors) ? $authors : [];
$csrf = (string) ($csrf_token ?? '');
?>
<div x-data="{ modal: null }" @open-author.window="modal = 'edit'" class="space-y-4">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold text-slate-950">Blog authors</h1><p class="mt-0.5 text-xs text-slate-500">Manage article bylines, profiles, and publishing status.</p></div>
        <div class="flex gap-2"><a href="/admin/blog" class="inline-flex h-10 min-w-28 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm">Back to posts</a><button type="button" @click="modal = 'create'" onclick="resetAuthorCreate()" class="inline-flex h-10 min-w-28 items-center justify-center rounded-lg bg-blue-700 px-4 text-sm font-bold !text-white shadow-sm">+ Add author</button></div>
    </header>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3"><div><h2 class="text-sm font-bold text-slate-950">Author directory</h2><p class="text-[11px] text-slate-400">Showing up to 10 records per page</p></div><span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600"><?php echo (int) ($pagination['total'] ?? count($authors)); ?> total</span></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-[11px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-2.5">Author</th><th class="px-4 py-2.5">Contact</th><th class="px-4 py-2.5">Posts</th><th class="px-4 py-2.5">Status</th><th class="px-4 py-2.5 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($authors as $author) : ?>
                        <?php $payload = htmlspecialchars(json_encode($author, JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}', ENT_QUOTES, 'UTF-8'); ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-2.5"><div class="flex items-center gap-3"><?php if (!empty($author['avatar'])) : ?><img src="<?php echo $this->escape((string) $author['avatar']); ?>" alt="" class="h-9 w-9 rounded-full object-cover"><?php else : ?><span class="grid h-9 w-9 place-items-center rounded-full bg-violet-50 text-xs font-bold text-violet-700"><?php echo $this->escape(substr((string) $author['display_name'], 0, 1)); ?></span><?php endif; ?><div><p class="font-semibold text-slate-900"><?php echo $this->escape((string) $author['display_name']); ?></p><p class="text-[11px] text-slate-400"><?php echo $this->escape((string) ($author['title'] ?? 'Author')); ?></p></div></div></td>
                            <td class="px-4 py-2.5"><p class="text-xs text-slate-600"><?php echo $this->escape((string) ($author['email'] ?? '—')); ?></p><p class="font-mono text-[10px] text-slate-400">/<?php echo $this->escape((string) $author['slug']); ?></p></td>
                            <td class="px-4 py-2.5 text-slate-600"><?php echo (int) ($author['post_count'] ?? 0); ?></td>
                            <td class="px-4 py-2.5">
                                <form method="post" action="/admin/blog/authors/<?php echo (int) $author['id']; ?>">
                                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>"><?php foreach (['display_name','slug','title','email','bio','avatar','linkedin_url','x_url'] as $key) : ?><input type="hidden" name="<?php echo $key; ?>" value="<?php echo $this->escape((string) ($author[$key] ?? '')); ?>"><?php endforeach; ?><input type="hidden" name="is_active" value="<?php echo empty($author['is_active']) ? '1' : '0'; ?>">
                                    <button class="rounded-full px-2.5 py-1 text-[10px] font-bold <?php echo !empty($author['is_active']) ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'; ?>"><?php echo !empty($author['is_active']) ? 'Active' : 'Inactive'; ?></button>
                                </form>
                            </td>
                            <td class="px-4 py-2.5"><div class="flex justify-end gap-3"><button type="button" data-record="<?php echo $payload; ?>" onclick="openAuthorEditor(this)" class="text-xs font-semibold text-blue-700">Edit</button><form method="post" action="/admin/blog/authors/<?php echo (int) $author['id']; ?>/delete" onsubmit="return confirm('Disable this author? Existing posts remain assigned.')"><input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>"><button class="text-xs font-semibold text-red-600">Delete</button></form></div></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($authors === []) : ?><tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">No authors found.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php echo $this->partial('admin/partials/pagination', ['pagination' => $pagination ?? [], 'filters' => []]); ?>

    <div x-show="modal" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/55 p-4" @keydown.escape.window="modal = null">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl" @click.outside="modal = null">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><h2 id="author-modal-title" class="font-bold text-slate-950">Add author</h2><button type="button" @click="modal = null" class="text-slate-400">✕</button></div>
            <form id="author-form" method="post" action="/admin/blog/authors" class="grid gap-3 p-5">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>"><input type="hidden" id="author-avatar" name="avatar" value="">
                <div class="grid gap-3 sm:grid-cols-2"><label><span class="text-xs font-bold text-slate-600">Display name</span><input id="author-name" name="display_name" required class="mt-1 h-10 w-full rounded-lg border-slate-300"></label><label><span class="text-xs font-bold text-slate-600">Slug</span><input id="author-slug" name="slug" class="mt-1 h-10 w-full rounded-lg border-slate-300 font-mono text-sm"></label><label><span class="text-xs font-bold text-slate-600">Role or title</span><input id="author-title" name="title" class="mt-1 h-10 w-full rounded-lg border-slate-300"></label><label><span class="text-xs font-bold text-slate-600">Email</span><input id="author-email" name="email" type="email" class="mt-1 h-10 w-full rounded-lg border-slate-300"></label></div>
                <label><span class="text-xs font-bold text-slate-600">Biography</span><textarea id="author-bio" name="bio" rows="2" class="mt-1 w-full rounded-lg border-slate-300"></textarea></label>
                <div class="grid gap-3 sm:grid-cols-2"><label><span class="text-xs font-bold text-slate-600">LinkedIn URL</span><input id="author-linkedin" name="linkedin_url" type="url" class="mt-1 h-10 w-full rounded-lg border-slate-300"></label><label><span class="text-xs font-bold text-slate-600">X URL</span><input id="author-x" name="x_url" type="url" class="mt-1 h-10 w-full rounded-lg border-slate-300"></label></div>
                <div class="flex flex-wrap items-center justify-between gap-3"><div class="flex items-center gap-3"><img id="author-avatar-preview" src="" alt="" class="hidden h-10 w-10 rounded-full object-cover"><button type="button" onclick="openMediaPicker('author-avatar','author-avatar-preview')" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700">Select avatar</button><label class="flex items-center gap-2 text-sm"><input id="author-active" type="checkbox" name="is_active" value="1" checked> Active</label></div><div class="flex gap-2"><button type="button" @click="modal = null" class="px-3 py-2 text-sm font-semibold text-slate-600">Cancel</button><button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Save author</button></div></div>
            </form>
        </div>
    </div>
</div>
<?php echo $this->partial('admin/partials/media-picker'); ?>
<script>
function resetAuthorCreate() {
    const form = document.getElementById('author-form'); form.reset(); form.action = '/admin/blog/authors';
    document.getElementById('author-modal-title').textContent = 'Add author';
    document.getElementById('author-avatar').value = '';
    const preview = document.getElementById('author-avatar-preview'); preview.src = ''; preview.classList.add('hidden');
}
function openAuthorEditor(button) {
    const a = JSON.parse(button.dataset.record), form = document.getElementById('author-form');
    form.action = '/admin/blog/authors/' + a.id; document.getElementById('author-modal-title').textContent = 'Edit author';
    const values = { 'author-name':a.display_name, 'author-slug':a.slug, 'author-title':a.title, 'author-email':a.email, 'author-bio':a.bio, 'author-linkedin':a.linkedin_url, 'author-x':a.x_url, 'author-avatar':a.avatar };
    Object.entries(values).forEach(([id,value]) => document.getElementById(id).value = value || '');
    const preview = document.getElementById('author-avatar-preview'); preview.src = a.avatar || ''; preview.classList.toggle('hidden', !a.avatar);
    document.getElementById('author-active').checked = Number(a.is_active) === 1; window.dispatchEvent(new CustomEvent('open-author'));
}
</script>
