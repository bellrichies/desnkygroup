<?php
$service ??= [];
$action ??= '/admin/services';
$image = (string) ($service['featured_image'] ?? '');
?>
<form method="POST" action="<?php echo $this->escape($action); ?>" id="service-form" class="pb-20">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
    <header class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">Service editor</p><h1 class="mt-1 text-2xl font-bold text-slate-950"><?php echo $this->escape((string) ($service['title'] ?? 'New service')); ?></h1></div>
        <div class="flex flex-wrap gap-2">
            <?php if (!empty($service['id'])) : ?><a href="/admin/services/<?php echo (int) $service['id']; ?>/heroes" class="rounded-xl bg-violet-50 px-4 py-2.5 text-sm font-bold text-violet-700">Manage hero slides</a><?php endif; ?>
            <a href="/admin/services" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-600">Back</a>
        </div>
    </header>
    <div class="grid gap-3 xl:grid-cols-[minmax(0,1.5fr)_minmax(20rem,.7fr)]">
        <div class="space-y-3">
            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3"><h2 class="font-bold">Service details</h2><p class="text-xs text-slate-500">Identity and listing content.</p></div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="sm:col-span-2"><span class="text-sm font-bold text-slate-700">Title <span class="text-red-600">*</span></span><input id="service-title" name="title" required maxlength="255" value="<?php echo $this->escape((string) ($service['title'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. Engineering Services"></label>
                    <label><span class="text-sm font-bold text-slate-700">Slug</span><input id="service-slug" name="slug" value="<?php echo $this->escape((string) ($service['slug'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 font-mono text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="engineering-services"></label>
                    <label><span class="text-sm font-bold text-slate-700">Category</span><input name="category" value="<?php echo $this->escape((string) ($service['category'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="Engineering"></label>
                    <label><span class="text-sm font-bold text-slate-700">Icon label</span><input name="icon" value="<?php echo $this->escape((string) ($service['icon'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="wrench"></label>
                    <label><span class="text-sm font-bold text-slate-700">Display order</span><input type="number" min="0" name="sort_order" value="<?php echo (int) ($service['sort_order'] ?? 0); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600"></label>
                    <label class="sm:col-span-2"><span class="text-sm font-bold text-slate-700">Summary</span><textarea name="summary" maxlength="500" rows="3" class="mt-1.5 w-full rounded-xl border-slate-300 px-3 py-2 focus:border-blue-600 focus:ring-blue-600" placeholder="Short description used on service cards and as a fallback."><?php echo $this->escape((string) ($service['summary'] ?? '')); ?></textarea></label>
                </div>
            </section>
            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3"><h2 class="font-bold">Page content</h2><p class="text-xs text-slate-500">Detailed fallback content for the public page.</p></div>
                <div id="quill-toolbar" class="rounded-t-xl border border-slate-300 bg-slate-50"><span class="ql-formats"><select class="ql-header"><option value="2"></option><option value="3"></option><option selected></option></select></span><span class="ql-formats"><button class="ql-bold"></button><button class="ql-italic"></button><button class="ql-underline"></button></span><span class="ql-formats"><button class="ql-list" value="ordered"></button><button class="ql-list" value="bullet"></button><button class="ql-link"></button><button class="ql-clean"></button></span></div>
                <div id="quill-editor" class="min-h-48 rounded-b-xl border border-t-0 border-slate-300 bg-white px-3 py-2 text-sm"></div>
                <textarea name="content" id="service-content" class="hidden"><?php echo $this->escape((string) ($service['content'] ?? '')); ?></textarea>
            </section>
        </div>
        <aside class="space-y-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="font-bold">Publishing</h2><label class="mt-4 flex items-center justify-between gap-3"><span><strong class="block text-sm">Published</strong><small class="text-slate-500">Visible on the website</small></span><input type="checkbox" name="is_published" value="1" <?php echo !empty($service['is_published']) ? 'checked' : ''; ?> class="h-5 w-5 rounded border-slate-300 text-blue-700 focus:ring-blue-600"></label></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold">Listing image</h2>
                <div class="mt-3 overflow-hidden rounded-xl bg-slate-100"><img id="featured_img_preview" src="<?php echo $this->escape($image); ?>" alt="Featured image preview" class="<?php echo $image ? '' : 'hidden '; ?>h-28 w-full object-cover"></div>
                <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $this->escape($image); ?>">
                <div class="mt-3 grid gap-2">
                    <button type="button" onclick="openMediaPicker('featured_image','featured_img_preview')" class="w-full rounded-xl bg-blue-700 px-3 py-2.5 text-sm font-bold !text-white shadow-sm hover:bg-blue-800"><?php echo $image ? 'Change image from Media Library' : 'Select from Media Library'; ?></button>
                    <?php if ($image !== '') : ?><button type="button" onclick="removeFeaturedImage()" class="rounded-xl px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50">Remove image</button><?php endif; ?>
                </div>
                <p class="mt-2 text-xs text-slate-500">Select an existing image asset. Hero backgrounds remain managed under Hero slides.</p>
            </section>
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="font-bold">Search & sharing</h2><div class="mt-4 grid gap-3">
                <label><span class="text-sm font-bold text-slate-700">Meta title</span><input name="meta_title" maxlength="255" value="<?php echo $this->escape((string) ($service['meta_title'] ?? '')); ?>" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm"></label>
                <label><span class="text-sm font-bold text-slate-700">Meta description</span><textarea name="meta_description" rows="3" class="mt-1 w-full rounded-xl border-slate-300 px-3 py-2 text-sm"><?php echo $this->escape((string) ($service['meta_description'] ?? '')); ?></textarea></label>
                <label><span class="text-sm font-bold text-slate-700">Keywords</span><input name="meta_keywords" value="<?php echo $this->escape((string) ($service['meta_keywords'] ?? '')); ?>" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm"></label>
                <label><span class="text-sm font-bold text-slate-700">Canonical URL</span><input type="url" name="canonical_url" value="<?php echo $this->escape((string) ($service['canonical_url'] ?? '')); ?>" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm"></label>
                <label><span class="text-sm font-bold text-slate-700">Social image path</span><input name="og_image" value="<?php echo $this->escape((string) ($service['og_image'] ?? '')); ?>" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm"></label>
            </div></section>
        </aside>
    </div>
    <div class="fixed bottom-0 right-0 z-20 flex w-full justify-end gap-3 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(15,23,42,.08)] backdrop-blur lg:left-72 lg:w-auto"><a href="/admin/services" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600">Cancel</a><button class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white">Save service</button></div>
</form>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css">
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
const titleInput = document.getElementById('service-title'), slugInput = document.getElementById('service-slug');
let slugTouched = slugInput.value !== ''; slugInput.addEventListener('input', () => slugTouched = true);
titleInput.addEventListener('input', () => { if (!slugTouched) slugInput.value = titleInput.value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''); });
const editor = new Quill('#quill-editor', { theme: 'snow', modules: { toolbar: '#quill-toolbar' }, placeholder: 'Detailed service description…' });
const contentField = document.getElementById('service-content'); editor.root.innerHTML = contentField.value;
document.getElementById('service-form').addEventListener('submit', () => contentField.value = editor.root.innerHTML);
function removeFeaturedImage() { document.getElementById('featured_image').value = ''; document.getElementById('featured_img_preview').classList.add('hidden'); }
</script>
<?php echo $this->partial('admin/partials/media-picker'); ?>
