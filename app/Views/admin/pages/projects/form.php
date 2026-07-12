<?php
$project       ??= [];
$galleryImages ??= [];
$action        ??= '/admin/projects';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/projects" class="text-sm font-semibold text-blue-700">← Back to projects</a>
</div>

<form method="POST" action="<?php echo $this->escape($action); ?>" id="project-form" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">

    <!-- ── Main column ───────────────────────────────────────────────────── -->
    <div class="space-y-6">

        <!-- Title + Slug + Category -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Project details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Title <span class="text-red-600">*</span></span>
                    <input
                        id="project-title"
                        name="title"
                        value="<?php echo $this->escape((string) ($project['title'] ?? '')); ?>"
                        required
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="e.g. Pipeline Integrity Inspection – Rivers State"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Slug</span>
                    <input
                        id="project-slug"
                        name="slug"
                        value="<?php echo $this->escape((string) ($project['slug'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="auto-generated"
                    >
                    <span class="mt-1 block text-xs text-gray-400">Auto-generated from title. Edit to customise.</span>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Category</span>
                    <input name="category" value="<?php echo $this->escape((string) ($project['category'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. Engineering">
                </label>
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Summary</span>
                    <textarea name="summary" rows="2" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Short one-liner for cards and listings."><?php echo $this->escape((string) ($project['summary'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>

        <!-- Description – WYSIWYG -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Description</h2>
            <div class="mt-4">
                <!-- Quill toolbar target -->
                <div id="quill-toolbar" class="rounded-t border border-gray-300 bg-gray-50">
                    <span class="ql-formats">
                        <select class="ql-header"><option value="2"></option><option value="3"></option><option selected></option></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link"></button>
                        <button class="ql-blockquote"></button>
                        <button class="ql-clean"></button>
                    </span>
                </div>
                <div id="quill-editor" class="min-h-[14rem] rounded-b border border-t-0 border-gray-300 bg-white px-3 py-2 text-sm leading-relaxed"></div>
                <!-- Hidden textarea submitted with the form -->
                <textarea name="description" id="project-description" class="hidden"><?php echo $this->escape((string) ($project['description'] ?? '')); ?></textarea>
            </div>
        </section>

        <!-- Image gallery -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200" id="gallery-section">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Gallery images</h2>
                <button type="button" id="add-gallery-image" class="rounded bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700 hover:bg-blue-100">+ Add images</button>
            </div>
            <p class="mt-1 text-xs text-gray-400">Drag rows to reorder. Each image can have optional alt text.</p>
            <div id="gallery-list" class="mt-4 space-y-3">
                <?php foreach ($galleryImages as $i => $img) : ?>
                    <div class="gallery-row flex items-center gap-3 rounded border border-gray-200 bg-gray-50 p-3" data-index="<?php echo $i; ?>">
                        <span class="cursor-move select-none text-gray-400" aria-hidden="true">⠿</span>
                        <img src="<?php echo $this->escape((string) $img['path']); ?>" alt="" class="h-14 w-20 rounded object-cover">
                        <div class="flex flex-1 flex-col gap-1">
                            <input type="hidden" name="gallery[<?php echo $i; ?>][path]" value="<?php echo $this->escape((string) $img['path']); ?>">
                            <input type="hidden" name="gallery[<?php echo $i; ?>][sort_order]" class="sort-order-input" value="<?php echo $i; ?>">
                            <input name="gallery[<?php echo $i; ?>][alt_text]" value="<?php echo $this->escape((string) ($img['alt_text'] ?? '')); ?>" placeholder="Alt text (optional)" class="rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        </div>
                        <button type="button" class="remove-gallery-row shrink-0 rounded p-1 text-red-500 hover:bg-red-50" aria-label="Remove">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="gallery-empty" class="<?php echo empty($galleryImages) ? '' : 'hidden'; ?> mt-4 rounded border border-dashed border-gray-300 p-6 text-center text-sm text-gray-400">
                No gallery images yet. Click "Add image" to get started.
            </div>
        </section>

    </div><!-- /main column -->

    <!-- ── Sidebar ───────────────────────────────────────────────────────── -->
    <aside class="space-y-5">

        <!-- Publishing -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Publishing</h2>
            <label class="mt-4 flex items-center gap-3 text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_published" value="1" <?php echo !empty($project['is_published']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700 focus:ring-blue-600">
                Published
            </label>
            <div class="mt-5 flex gap-3">
                <button class="flex-1 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save</button>
                <a href="/admin/projects" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </section>

        <!-- Featured image -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Featured image</h2>
            <div class="mt-4 space-y-3">
                <?php $fi = (string) ($project['featured_image'] ?? ''); ?>
                <img
                    id="featured_img_preview"
                    src="<?php echo $this->escape($fi); ?>"
                    alt="Featured image preview"
                    class="<?php echo $fi ? '' : 'hidden'; ?> w-full rounded object-cover"
                    style="max-height:160px"
                >
                <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $this->escape($fi); ?>">
                <button
                    type="button"
                    onclick="openMediaPicker('featured_image','featured_img_preview')"
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <?php echo $fi ? 'Change image' : 'Select from library'; ?>
                </button>
                <?php if ($fi) : ?>
                    <button type="button" onclick="document.getElementById('featured_image').value='';document.getElementById('featured_img_preview').classList.add('hidden');" class="w-full rounded border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Remove image</button>
                <?php endif; ?>
            </div>
        </section>

        <!-- Client / date / sort -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Project meta</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Client</span>
                    <input name="client_name" value="<?php echo $this->escape((string) ($project['client_name'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Organisation name">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Project date</span>
                    <input type="date" name="project_date" value="<?php echo $this->escape((string) ($project['project_date'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Sort order</span>
                    <input type="number" name="sort_order" value="<?php echo (int) ($project['sort_order'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
            </div>
        </section>

        <!-- SEO -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">SEO</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">SEO title</span>
                    <input name="meta_title" value="<?php echo $this->escape((string) ($project['meta_title'] ?? '')); ?>" placeholder="Leave blank to use project title" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Meta description</span>
                    <textarea name="meta_description" rows="3" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Up to 160 characters"><?php echo $this->escape((string) ($project['meta_description'] ?? '')); ?></textarea>
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Keywords</span>
                    <input name="meta_keywords" value="<?php echo $this->escape((string) ($project['meta_keywords'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Canonical URL</span>
                    <input name="canonical_url" type="url" value="<?php echo $this->escape((string) ($project['canonical_url'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">OG image</span>
                    <?php $ogImg = (string) ($project['og_image'] ?? ''); ?>
                    <img id="og_img_preview" src="<?php echo $this->escape($ogImg); ?>" alt="" class="<?php echo $ogImg ? '' : 'hidden'; ?> mb-2 w-full rounded object-cover" style="max-height:100px">
                    <input type="hidden" id="og_image" name="og_image" value="<?php echo $this->escape($ogImg); ?>">
                    <button type="button" onclick="openMediaPicker('og_image','og_img_preview')" class="w-full rounded border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        <?php echo $ogImg ? 'Change OG image' : 'Select OG image'; ?>
                    </button>
                </label>
            </div>
        </section>

    </aside>
</form>

<!-- Media picker modal (rendered once per page) -->
<?php echo $this->partial('admin/partials/media-picker'); ?>

<!-- Gallery row template (cloned by JS) -->
<template id="gallery-row-tpl">
    <div class="gallery-row flex items-center gap-3 rounded border border-gray-200 bg-gray-50 p-3">
        <span class="cursor-move select-none text-gray-400" aria-hidden="true">⠿</span>
        <img src="" alt="" class="h-14 w-20 rounded object-cover">
        <div class="flex flex-1 flex-col gap-1">
            <input type="hidden" name="" value="">
            <input type="hidden" name="" class="sort-order-input" value="">
            <input name="" placeholder="Alt text (optional)" class="rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
        </div>
        <button type="button" class="remove-gallery-row shrink-0 rounded p-1 text-red-500 hover:bg-red-50" aria-label="Remove">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</template>

<!-- Quill WYSIWYG -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
(function () {
    // ── Slug auto-generation ───────────────────────────────────────────────
    const titleInput = document.getElementById('project-title');
    const slugInput  = document.getElementById('project-slug');
    let slugEdited   = slugInput.value !== '';

    function toSlug(str) {
        return str.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    titleInput.addEventListener('input', () => {
        if (!slugEdited) slugInput.value = toSlug(titleInput.value);
    });
    slugInput.addEventListener('input', () => { slugEdited = slugInput.value !== ''; });

    // ── Quill WYSIWYG ─────────────────────────────────────────────────────
    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        modules: { toolbar: '#quill-toolbar' },
        placeholder: 'Detailed project description…',
    });

    const textarea = document.getElementById('project-description');
    // Populate editor with existing HTML content
    if (textarea.value.trim()) {
        quill.root.innerHTML = textarea.value;
    }

    document.getElementById('project-form').addEventListener('formdata', e => {
        e.formData.set('description', quill.root.innerHTML);
    });
    // Fallback for browsers without FormData event
    document.getElementById('project-form').addEventListener('submit', () => {
        textarea.value = quill.root.innerHTML;
    });

    // ── Gallery management ─────────────────────────────────────────────────
    const list   = document.getElementById('gallery-list');
    const empty  = document.getElementById('gallery-empty');
    const addBtn = document.getElementById('add-gallery-image');
    const tpl    = document.getElementById('gallery-row-tpl');

    function refreshEmpty() {
        empty.classList.toggle('hidden', list.children.length > 0);
    }

    function reindex() {
        list.querySelectorAll('.gallery-row').forEach((row, i) => {
            row.dataset.index = i;
            row.querySelectorAll('input').forEach(inp => {
                if (inp.name) inp.name = inp.name.replace(/gallery\[\d+\]/, `gallery[${i}]`);
            });
            const so = row.querySelector('.sort-order-input');
            if (so) so.value = i;
        });
    }

    function addGalleryRow(path, alt) {
        const row = tpl.content.cloneNode(true).querySelector('.gallery-row');
        const i   = list.querySelectorAll('.gallery-row').length;
        row.querySelector('img').src = path;
        const [pathInp, sortInp, altInp] = row.querySelectorAll('input');
        pathInp.name  = `gallery[${i}][path]`;
        pathInp.value = path;
        sortInp.name  = `gallery[${i}][sort_order]`;
        sortInp.value = i;
        altInp.name   = `gallery[${i}][alt_text]`;
        altInp.value  = alt || '';
        list.appendChild(row);
        refreshEmpty();
    }

    list.addEventListener('click', e => {
        const btn = e.target.closest('.remove-gallery-row');
        if (btn) {
            btn.closest('.gallery-row').remove();
            reindex();
            refreshEmpty();
        }
    });

    addBtn.addEventListener('click', () => {
        openMediaPickerMulti(images => images.forEach(img => addGalleryRow(img.path, img.alt)));
    });

    refreshEmpty();
})();
</script>
