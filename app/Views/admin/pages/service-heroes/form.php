<?php
$hero ??= [];
$mediaPath = (string) ($hero['background_media'] ?? '');
$mediaType = (string) ($hero['media_type'] ?? 'image');
?>
<form method="POST" action="<?php echo $this->escape($action); ?>" enctype="multipart/form-data" id="hero-form" class="pb-24">
    <?php echo $this->csrfField(); ?>
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">Hero editor</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950"><?php echo $this->escape((string) $service['title']); ?></h1>
        </div>
        <a href="/admin/services/<?php echo (int) $service['id']; ?>/heroes" class="text-sm font-bold text-slate-600 hover:text-blue-700">← Back to slides</a>
    </div>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.45fr)_minmax(20rem,.75fr)]">
        <div class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700">01</span>
                    <div><h2 class="font-bold text-slate-900">Hero message</h2><p class="text-xs text-slate-500">The key content visitors see first.</p></div>
                </div>
                <div class="grid gap-4">
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Main heading <span class="text-red-600">*</span></span>
                        <input name="heading" required maxlength="255" value="<?php echo $this->escape((string) ($hero['heading'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="A clear, benefit-led headline">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Subheading</span>
                        <input name="subheading" maxlength="500" value="<?php echo $this->escape((string) ($hero['subheading'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="One concise supporting statement">
                    </label>
                    <label class="block">
                        <span class="text-sm font-bold text-slate-700">Description</span>
                        <textarea name="description" rows="4" class="mt-1.5 w-full rounded-xl border-slate-300 px-3 py-2 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Add context in two or three short sentences."><?php echo $this->escape((string) ($hero['description'] ?? '')); ?></textarea>
                    </label>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 font-bold text-violet-700">02</span>
                    <div><h2 class="font-bold text-slate-900">Calls to action</h2><p class="text-xs text-slate-500">Leave both fields in a pair empty to hide that button.</p></div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <label><span class="text-sm font-bold text-slate-700">Primary button text</span><input name="primary_cta_label" maxlength="120" value="<?php echo $this->escape((string) ($hero['primary_cta_label'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="Request a consultation"></label>
                    <label><span class="text-sm font-bold text-slate-700">Primary button link</span><input name="primary_cta_url" value="<?php echo $this->escape((string) ($hero['primary_cta_url'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="/contact"></label>
                    <label><span class="text-sm font-bold text-slate-700">Secondary button text</span><input name="secondary_cta_label" maxlength="120" value="<?php echo $this->escape((string) ($hero['secondary_cta_label'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="Explore capabilities"></label>
                    <label><span class="text-sm font-bold text-slate-700">Secondary button link</span><input name="secondary_cta_url" value="<?php echo $this->escape((string) ($hero['secondary_cta_url'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="#service-content"></label>
                </div>
            </section>
        </div>

        <aside class="space-y-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Background media</h2>
                <div class="mt-4 grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1">
                    <label class="cursor-pointer"><input type="radio" name="media_type" value="image" <?php echo $mediaType === 'image' ? 'checked' : ''; ?> class="peer sr-only"><span class="block rounded-lg px-3 py-2 text-center text-sm font-bold text-slate-600 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow">Image</span></label>
                    <label class="cursor-pointer"><input type="radio" name="media_type" value="video" <?php echo $mediaType === 'video' ? 'checked' : ''; ?> class="peer sr-only"><span class="block rounded-lg px-3 py-2 text-center text-sm font-bold text-slate-600 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow">Video URL</span></label>
                </div>
                <div id="drop-zone" class="mt-4 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/40">
                    <div id="media-preview" class="<?php echo $mediaPath === '' ? 'hidden ' : ''; ?>relative mb-3 overflow-hidden rounded-lg bg-slate-900">
                        <img id="preview-image" src="<?php echo $this->escape($mediaType === 'image' ? $mediaPath : ''); ?>" alt="Hero preview" class="<?php echo $mediaType === 'image' && $mediaPath !== '' ? '' : 'hidden '; ?>h-40 w-full object-cover">
                        <video id="preview-video" src="<?php echo $this->escape($mediaType === 'video' ? $mediaPath : ''); ?>" class="<?php echo $mediaType === 'video' && $mediaPath !== '' ? '' : 'hidden '; ?>h-40 w-full object-cover" muted controls></video>
                    </div>
                    <p class="text-sm font-bold text-slate-700">Drop an image here</p>
                    <p class="mt-1 text-xs text-slate-500">JPG, PNG or WebP · optimized on upload · maximum 5 MB</p>
                    <label class="mt-3 inline-flex cursor-pointer rounded-lg bg-white px-3 py-2 text-sm font-bold text-blue-700 shadow-sm ring-1 ring-slate-200">
                        Choose image<input id="background-upload" name="background_upload" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    </label>
                </div>
                <label class="mt-4 block"><span class="text-sm font-bold text-slate-700">Media path or video URL</span><input id="background-media" name="background_media" value="<?php echo $this->escape($mediaPath); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 text-sm focus:border-blue-600 focus:ring-blue-600" placeholder="/uploads/media/image.webp"></label>
                <label class="mt-3 flex items-center gap-2 text-sm font-semibold text-red-600"><input type="checkbox" name="remove_background_media" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500"> Remove current media</label>
                <div class="mt-4 hidden h-2 overflow-hidden rounded-full bg-slate-200" id="upload-track"><div id="upload-progress" class="h-full bg-blue-600 transition-all" style="width:0"></div></div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-slate-900">Publishing</h2>
                <label class="mt-4 flex items-center justify-between gap-3"><span><strong class="block text-sm text-slate-800">Visible section</strong><small class="text-slate-500">Show the hero area publicly</small></span><input type="checkbox" name="is_visible" value="1" <?php echo !isset($hero['is_visible']) || !empty($hero['is_visible']) ? 'checked' : ''; ?> class="h-5 w-5 rounded border-slate-300 text-blue-700 focus:ring-blue-600"></label>
                <label class="mt-4 flex items-center justify-between gap-3"><span><strong class="block text-sm text-slate-800">Slide enabled</strong><small class="text-slate-500">Include this item in rotation</small></span><input type="checkbox" name="is_active" value="1" <?php echo !isset($hero['is_active']) || !empty($hero['is_active']) ? 'checked' : ''; ?> class="h-5 w-5 rounded border-slate-300 text-blue-700 focus:ring-blue-600"></label>
                <label class="mt-4 block"><span class="text-sm font-bold text-slate-700">Display order</span><input type="number" min="0" name="sort_order" value="<?php echo (int) ($hero['sort_order'] ?? 0); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600"></label>
            </section>
        </aside>
    </div>

    <div class="fixed bottom-0 right-0 z-20 flex w-full items-center justify-end gap-3 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(15,23,42,.08)] backdrop-blur lg:left-72 lg:w-auto">
        <a href="/admin/services/<?php echo (int) $service['id']; ?>/heroes" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancel</a>
        <button id="save-button" class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800">Save hero slide</button>
    </div>
</form>
<script>
(() => {
    const input = document.getElementById('background-upload');
    const zone = document.getElementById('drop-zone');
    const preview = document.getElementById('media-preview');
    const image = document.getElementById('preview-image');
    const maxBytes = 5 * 1024 * 1024;
    const showFile = file => {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > maxBytes) {
            input.setCustomValidity('Choose a JPG, PNG or WebP image no larger than 5 MB.');
            input.reportValidity();
            return;
        }
        input.setCustomValidity('');
        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
        image.src = URL.createObjectURL(file);
        image.classList.remove('hidden');
        document.getElementById('preview-video').classList.add('hidden');
        preview.classList.remove('hidden');
    };
    input.addEventListener('change', () => input.files[0] && showFile(input.files[0]));
    ['dragenter', 'dragover'].forEach(type => zone.addEventListener(type, event => {
        event.preventDefault(); zone.classList.add('border-blue-500', 'bg-blue-50');
    }));
    ['dragleave', 'drop'].forEach(type => zone.addEventListener(type, event => {
        event.preventDefault(); zone.classList.remove('border-blue-500', 'bg-blue-50');
    }));
    zone.addEventListener('drop', event => event.dataTransfer.files[0] && showFile(event.dataTransfer.files[0]));
    document.getElementById('hero-form').addEventListener('submit', event => {
        if (!event.currentTarget.checkValidity() || !input.files.length) return;
        event.preventDefault();
        const xhr = new XMLHttpRequest();
        const track = document.getElementById('upload-track');
        const bar = document.getElementById('upload-progress');
        const button = document.getElementById('save-button');
        track.classList.remove('hidden'); button.disabled = true; button.textContent = 'Uploading…';
        xhr.upload.addEventListener('progress', e => {
            if (e.lengthComputable) bar.style.width = Math.round((e.loaded / e.total) * 100) + '%';
        });
        xhr.addEventListener('load', () => window.location.href = xhr.responseURL || '/admin/services/<?php echo (int) $service['id']; ?>/heroes');
        xhr.addEventListener('error', () => { button.disabled = false; button.textContent = 'Try again'; });
        xhr.open('POST', event.currentTarget.action);
        xhr.send(new FormData(event.currentTarget));
    });
})();
</script>
