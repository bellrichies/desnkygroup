<?php
$hero ??= [];
$mediaPath = (string) ($hero['background_media'] ?? '');
$mediaType = (string) ($hero['media_type'] ?? 'image');
$selectedMedia ??= null;
?>
<form method="POST" action="<?php echo $this->escape($action); ?>" id="hero-form" class="pb-20">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-blue-700">Hero editor</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950"><?php echo $this->escape((string) $service['title']); ?></h1>
        </div>
        <a href="/admin/services/<?php echo (int) $service['id']; ?>/heroes" class="text-sm font-bold text-slate-600 hover:text-blue-700">← Back to slides</a>
    </div>

    <div class="grid gap-3 xl:grid-cols-[minmax(0,1.45fr)_minmax(20rem,.75fr)]">
        <div class="space-y-3">
            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3">
                    <div><h2 class="font-bold text-slate-900">Hero message</h2><p class="text-xs text-slate-500">The key content visitors see first.</p></div>
                </div>
                <div class="grid gap-3">
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

            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3">
                    <div><h2 class="font-bold text-slate-900">Calls to action</h2><p class="text-xs text-slate-500">Leave both fields in a pair empty to hide that button.</p></div>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <label><span class="text-sm font-bold text-slate-700">Primary button text</span><input name="primary_cta_label" maxlength="120" value="<?php echo $this->escape((string) ($hero['primary_cta_label'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="Request a consultation"></label>
                    <label><span class="text-sm font-bold text-slate-700">Primary button link</span><input name="primary_cta_url" value="<?php echo $this->escape((string) ($hero['primary_cta_url'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="/contact"></label>
                    <label><span class="text-sm font-bold text-slate-700">Secondary button text</span><input name="secondary_cta_label" maxlength="120" value="<?php echo $this->escape((string) ($hero['secondary_cta_label'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="Explore capabilities"></label>
                    <label><span class="text-sm font-bold text-slate-700">Secondary button link</span><input name="secondary_cta_url" value="<?php echo $this->escape((string) ($hero['secondary_cta_url'] ?? '')); ?>" class="mt-1.5 h-11 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600" placeholder="#service-content"></label>
                </div>
            </section>
        </div>

        <aside class="space-y-3">
            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h2 class="font-bold text-slate-900">Background media</h2>
                <p class="mt-1 text-xs text-slate-500">Choose a reusable image or video managed centrally in the Media Library.</p>
                <input type="hidden" id="background-media" name="background_media" value="<?php echo $this->escape($mediaPath); ?>">
                <input type="hidden" id="background-media-type" name="media_type" value="<?php echo $this->escape($mediaType); ?>">
                <div id="selected-media-card" class="<?php echo $mediaPath === '' ? 'hidden ' : ''; ?>mt-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                    <div class="flex h-36 items-center justify-center overflow-hidden bg-slate-950">
                        <img id="preview-image" src="<?php echo $this->escape($mediaType === 'image' ? $mediaPath : ''); ?>" alt="Selected hero background" class="<?php echo $mediaType === 'image' && $mediaPath !== '' ? '' : 'hidden '; ?>h-full w-full object-cover">
                        <video id="preview-video" src="<?php echo $this->escape($mediaType === 'video' ? $mediaPath : ''); ?>" class="<?php echo $mediaType === 'video' && $mediaPath !== '' ? '' : 'hidden '; ?>h-full w-full object-cover" muted controls preload="metadata"></video>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-3 py-2">
                        <p id="selected-media-meta" class="text-xs font-semibold text-slate-500"><?php echo $this->escape(ucfirst((string) ($selectedMedia['media_type'] ?? $mediaType))); ?><?php echo !empty($selectedMedia['width']) && !empty($selectedMedia['height']) ? ' · ' . (int) $selectedMedia['width'] . ' × ' . (int) $selectedMedia['height'] . ' px' : ''; ?></p>
                        <button type="button" id="remove-media" class="rounded-lg px-2 py-1 text-xs font-bold text-red-600 hover:bg-red-50">Remove</button>
                    </div>
                </div>
                <button type="button" id="select-media" class="mt-3 w-full rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-bold !text-white shadow-sm hover:bg-blue-800"><?php echo $mediaPath === '' ? 'Select from Media Library' : 'Replace from Media Library'; ?></button>
                <p class="mt-1.5 text-center text-[11px] text-slate-400">Upload new assets from the Media Library page first.</p>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h2 class="font-bold text-slate-900">Publishing</h2>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_visible" value="1" <?php echo !isset($hero['is_visible']) || !empty($hero['is_visible']) ? 'checked' : ''; ?> class="h-5 w-5 rounded border-slate-300 text-blue-700 focus:ring-blue-600"><span><strong class="block text-xs text-slate-800">Visible</strong><small class="text-[10px] text-slate-500">Publicly shown</small></span></label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" <?php echo !isset($hero['is_active']) || !empty($hero['is_active']) ? 'checked' : ''; ?> class="h-5 w-5 rounded border-slate-300 text-blue-700 focus:ring-blue-600"><span><strong class="block text-xs text-slate-800">Enabled</strong><small class="text-[10px] text-slate-500">In rotation</small></span></label>
                </div>
                <label class="mt-3 block"><span class="text-sm font-bold text-slate-700">Display order</span><input type="number" min="0" name="sort_order" value="<?php echo (int) ($hero['sort_order'] ?? 0); ?>" class="mt-1.5 h-10 w-full rounded-xl border-slate-300 px-3 focus:border-blue-600 focus:ring-blue-600"></label>
            </section>
        </aside>
    </div>

    <div class="fixed bottom-0 right-0 z-20 flex w-full items-center justify-end gap-3 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(15,23,42,.08)] backdrop-blur lg:left-72 lg:w-auto">
        <div id="save-feedback" class="mr-auto hidden rounded-lg px-3 py-2 text-sm font-semibold" role="status" aria-live="polite"></div>
        <a href="/admin/services/<?php echo (int) $service['id']; ?>/heroes" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancel</a>
        <button id="save-button" class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-800">Save hero slide</button>
    </div>
</form>
<?php echo $this->partial('admin/partials/media-picker'); ?>
<script>
(() => {
    // Background assets are selected exclusively through the shared Media Library picker.
})();
</script>
<script>
(() => {
    const mediaInput = document.getElementById('background-media');
    const typeInput = document.getElementById('background-media-type');
    const card = document.getElementById('selected-media-card');
    const image = document.getElementById('preview-image');
    const video = document.getElementById('preview-video');
    const selectButton = document.getElementById('select-media');
    const choose = item => {
        if (!['image', 'video'].includes(item.media_type)) return;
        mediaInput.value = item.path;
        typeInput.value = item.media_type;
        document.getElementById('selected-media-meta').textContent = item.media_type.charAt(0).toUpperCase() + item.media_type.slice(1) + (item.width && item.height ? ` · ${item.width} × ${item.height} px` : '');
        if (item.media_type === 'video') {
            video.src = item.path;
            video.classList.remove('hidden');
            image.classList.add('hidden');
        } else {
            image.src = item.path;
            image.alt = item.alt || item.name;
            image.classList.remove('hidden');
            video.classList.add('hidden');
            video.removeAttribute('src');
        }
        card.classList.remove('hidden');
        selectButton.textContent = 'Replace from Media Library';
    };
    selectButton.addEventListener('click', () => window.openMediaPickerWithCallback(choose, ['image', 'video']));
    document.getElementById('remove-media').addEventListener('click', () => {
        mediaInput.value = '';
        typeInput.value = 'image';
        image.removeAttribute('src');
        video.removeAttribute('src');
        card.classList.add('hidden');
        selectButton.textContent = 'Select from Media Library';
    });
    document.getElementById('hero-form').addEventListener('submit', async event => {
        event.preventDefault();
        const form = event.currentTarget;
        const button = document.getElementById('save-button');
        const feedback = document.getElementById('save-feedback');
        if (button.disabled || !form.reportValidity()) return;
        button.disabled = true;
        button.textContent = 'Saving…';
        feedback.classList.add('hidden');
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {Accept:'application/json', 'X-Requested-With':'XMLHttpRequest'},
                body: new FormData(form),
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'The hero slide could not be saved.');
            feedback.textContent = data.message;
            feedback.className = 'mr-auto rounded-lg bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700';
            if (data.edit_url && data.update_url && form.action.endsWith('/heroes')) {
                form.action = data.update_url;
                window.history.replaceState({}, '', data.edit_url);
            }
        } catch (error) {
            feedback.textContent = error.message;
            feedback.className = 'mr-auto rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-700';
        }
        button.disabled = false;
        button.textContent = 'Save hero slide';
    });
})();
</script>
