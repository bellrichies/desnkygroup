<?php
$service ??= [];
$action  ??= '/admin/services';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900"><?php echo $this->escape($title); ?></h1>
    <a href="/admin/services" class="text-sm font-semibold text-blue-700">← Back to services</a>
</div>

<form method="POST" action="<?php echo $this->escape($action); ?>" id="service-form" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">

    <!-- ── Main column ───────────────────────────────────────────────────── -->
    <div class="space-y-6">

        <!-- Title + Slug + Icon + Category + Summary -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Service details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Title <span class="text-red-600">*</span></span>
                    <input
                        id="service-title"
                        name="title"
                        value="<?php echo $this->escape((string) ($service['title'] ?? '')); ?>"
                        required
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="e.g. Pipeline Inspection"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Slug</span>
                    <input
                        id="service-slug"
                        name="slug"
                        value="<?php echo $this->escape((string) ($service['slug'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="auto-generated"
                    >
                    <span class="mt-1 block text-xs text-gray-400">Auto-generated from title. Edit to customise.</span>
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Icon label</span>
                    <input name="icon" value="<?php echo $this->escape((string) ($service['icon'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. wrench">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Category</span>
                    <input name="category" value="<?php echo $this->escape((string) ($service['category'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. Engineering">
                </label>
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Summary</span>
                    <textarea name="summary" rows="2" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Short one-liner for cards and listings."><?php echo $this->escape((string) ($service['summary'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>

        <!-- Content – WYSIWYG -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Content</h2>
            <div class="mt-4">
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
                <div id="quill-editor" class="min-h-[16rem] rounded-b border border-t-0 border-gray-300 bg-white px-3 py-2 text-sm leading-relaxed"></div>
                <textarea name="content" id="service-content" class="hidden"><?php echo $this->escape((string) ($service['content'] ?? '')); ?></textarea>
            </div>
        </section>

    </div><!-- /main column -->

    <!-- ── Sidebar ───────────────────────────────────────────────────────── -->
    <aside class="space-y-5">

        <!-- Publishing -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Publishing</h2>
            <label class="mt-4 flex items-center gap-3 text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_published" value="1" <?php echo !empty($service['is_published']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700 focus:ring-blue-600">
                Published
            </label>
            <div class="mt-5 flex gap-3">
                <button class="flex-1 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save</button>
                <a href="/admin/services" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </section>

        <!-- Featured image -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Featured image</h2>
            <div class="mt-4 space-y-3">
                <?php $fi = (string) ($service['featured_image'] ?? ''); ?>
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

        <!-- Service meta -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Service meta</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Sort order</span>
                    <input type="number" name="sort_order" value="<?php echo (int) ($service['sort_order'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
            </div>
        </section>

        <!-- SEO -->
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">SEO</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">SEO title</span>
                    <input name="meta_title" value="<?php echo $this->escape((string) ($service['meta_title'] ?? '')); ?>" placeholder="Leave blank to use service title" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Meta description</span>
                    <textarea name="meta_description" rows="3" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Up to 160 characters"><?php echo $this->escape((string) ($service['meta_description'] ?? '')); ?></textarea>
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Keywords</span>
                    <input name="meta_keywords" value="<?php echo $this->escape((string) ($service['meta_keywords'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Canonical URL</span>
                    <input name="canonical_url" type="url" value="<?php echo $this->escape((string) ($service['canonical_url'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">OG image</span>
                    <?php $ogImg = (string) ($service['og_image'] ?? ''); ?>
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

<!-- Quill WYSIWYG -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
(function () {
    // ── Slug auto-generation ───────────────────────────────────────────────
    const titleInput = document.getElementById('service-title');
    const slugInput  = document.getElementById('service-slug');
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
        placeholder: 'Detailed service description…',
    });

    const textarea = document.getElementById('service-content');
    if (textarea.value.trim()) {
        quill.root.innerHTML = textarea.value;
    }

    document.getElementById('service-form').addEventListener('formdata', e => {
        e.formData.set('content', quill.root.innerHTML);
    });
    document.getElementById('service-form').addEventListener('submit', () => {
        textarea.value = quill.root.innerHTML;
    });
})();
</script>
