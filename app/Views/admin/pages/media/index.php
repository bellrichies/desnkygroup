<?php
$media ??= [];
$total ??= count($media);
$hasMore ??= false;
$search ??= '';
$categoryCounts ??= ['all' => count($media), 'image' => 0, 'video' => 0, 'document' => 0];
$csrf = (string) ($_SESSION['csrf_token'] ?? '');
$formatBytes = static function (int $bytes): string {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1) . ' MB';
    }

    return number_format(max(0, $bytes) / 1024, 1) . ' KB';
};
?>
<div id="media-app" class="space-y-5" data-total="<?php echo (int) $total; ?>">
    <header class="flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-blue-950 p-5 text-white shadow-lg lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-200">Asset management</p>
            <h1 class="mt-2 text-2xl font-bold">Media Library</h1>
            <p class="mt-1 text-sm text-slate-300"><span id="media-total"><?php echo (int) $total; ?></span> optimized assets available across the website.</p>
        </div>
        <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
            <label class="relative block min-w-0 flex-1 lg:w-72">
                <span class="sr-only">Search media</span>
                <span class="pointer-events-none absolute left-3 top-2.5 text-slate-400">⌕</span>
                <input id="media-search" value="<?php echo $this->escape($search); ?>" class="h-10 w-full rounded-xl border-white/10 bg-white/10 pl-9 pr-3 text-sm text-white placeholder:text-slate-400 focus:border-blue-400 focus:ring-blue-400" placeholder="Search files, titles or alt text">
            </label>
            <button type="button" id="show-uploader" class="h-10 rounded-xl bg-blue-500 px-4 text-sm font-bold text-white shadow hover:bg-blue-400">Upload media</button>
        </div>
    </header>
    <div id="page-notice" role="status" class="fixed right-4 top-20 z-[60] hidden max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg"></div>

    <div id="upload-panel" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="upload-modal-title">
        <div id="upload-backdrop" class="absolute inset-0 bg-slate-950/60 opacity-0 backdrop-blur-sm transition-opacity duration-200"></div>
        <section id="upload-dialog" class="relative z-10 max-h-[90vh] w-full max-w-4xl translate-y-4 scale-[.98] overflow-y-auto rounded-2xl bg-white p-5 opacity-0 shadow-2xl transition duration-200">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 id="upload-modal-title" class="font-bold text-slate-900">Upload media</h2><p class="text-xs text-slate-500">Select JPG, PNG or WebP images up to 5 MB each.</p></div>
            <button type="button" data-close-upload class="flex h-9 w-9 items-center justify-center rounded-xl text-2xl leading-none text-slate-500 hover:bg-slate-100" aria-label="Close upload dialog">×</button>
        </div>
        <div class="mt-2 flex justify-end"><button type="button" id="clear-queue" class="hidden rounded-lg px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50">Clear selected</button></div>
        <div class="mt-4 grid gap-4 xl:grid-cols-[minmax(18rem,.7fr)_minmax(0,1.3fr)]">
            <div>
                <div id="media-dropzone" tabindex="0" role="button" aria-label="Select images to upload" class="flex min-h-40 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-7 text-center outline-none transition hover:border-blue-500 hover:bg-blue-50/50 focus-visible:ring-2 focus-visible:ring-blue-600">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-xl text-blue-700">↑</span>
                    <strong class="mt-3 text-sm text-slate-800">Drop images here</strong>
                    <span class="mt-1 text-xs text-slate-500">or click to browse your device</span>
                </div>
                <input id="media-files" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
                <div id="upload-alert" role="alert" class="mt-3 hidden rounded-xl px-3 py-2 text-sm"></div>
            </div>
            <div>
                <div id="upload-empty" class="flex min-h-40 items-center justify-center rounded-xl border border-slate-200 px-5 text-center text-sm text-slate-400">Selected image previews will appear here.</div>
                <div id="upload-queue" class="grid gap-3 sm:grid-cols-2 2xl:grid-cols-3"></div>
            </div>
        </div>
        <div id="upload-actions" class="mt-4 hidden flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
            <button type="button" data-close-upload class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
            <button type="button" id="upload-button" class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60">Upload selected</button>
        </div>
        </section>
    </div>

    <section aria-labelledby="library-heading">
        <div class="mb-3 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div><h2 id="library-heading" class="font-bold text-slate-900">All media</h2><p id="library-status" class="text-xs text-slate-500">Showing up to 20 items</p></div>
            <div id="media-filters" class="flex max-w-full gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1" aria-label="Filter media by type">
                <button type="button" data-category="all" aria-pressed="true" class="media-filter is-active"><span aria-hidden="true">▦</span> All <span data-count="all" class="media-filter-count"><?php echo (int) ($categoryCounts['all'] ?? 0); ?></span></button>
                <button type="button" data-category="image" aria-pressed="false" class="media-filter"><span aria-hidden="true">▧</span> Images <span data-count="image" class="media-filter-count"><?php echo (int) ($categoryCounts['image'] ?? 0); ?></span></button>
                <button type="button" data-category="video" aria-pressed="false" class="media-filter"><span aria-hidden="true">▷</span> Videos <span data-count="video" class="media-filter-count"><?php echo (int) ($categoryCounts['video'] ?? 0); ?></span></button>
                <button type="button" data-category="document" aria-pressed="false" class="media-filter"><span aria-hidden="true">▤</span> Documents <span data-count="document" class="media-filter-count"><?php echo (int) ($categoryCounts['document'] ?? 0); ?></span></button>
            </div>
            <div id="library-spinner" class="hidden text-sm font-semibold text-blue-700" role="status">Loading…</div>
        </div>
        <div id="media-grid" class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5">
            <?php foreach ($media as $item) : ?>
                <article class="media-card group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" data-id="<?php echo (int) $item['id']; ?>">
                    <button type="button" class="media-open relative block aspect-[4/3] w-full overflow-hidden bg-slate-100 text-left" data-id="<?php echo (int) $item['id']; ?>" aria-label="Open media details">
                        <?php if (($item['media_type'] ?? 'image') === 'image') : ?>
                            <img src="<?php echo $this->escape((string) $item['path']); ?>" alt="<?php echo $this->escape((string) ($item['alt_text'] ?? $item['title'] ?? $item['filename'])); ?>" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">
                        <?php else : ?>
                            <span class="flex h-full w-full items-center justify-center text-4xl text-slate-400" aria-hidden="true"><?php echo ($item['media_type'] ?? '') === 'video' ? '▷' : '▤'; ?></span>
                        <?php endif; ?>
                        <span class="absolute left-2 top-2 rounded-full bg-slate-950/75 px-2 py-1 text-[10px] font-bold uppercase text-white"><?php echo $this->escape(str_replace('image/', '', (string) ($item['mime_type'] ?? 'image'))); ?></span>
                    </button>
                    <div class="p-2.5">
                        <div class="flex items-center justify-between gap-2 text-[11px] text-slate-500">
                            <span><?php echo $formatBytes((int) ($item['size'] ?? 0)); ?></span>
                            <time datetime="<?php echo $this->escape((string) ($item['created_at'] ?? '')); ?>"><?php echo !empty($item['created_at']) ? date('M j, Y', strtotime((string) $item['created_at'])) : ''; ?></time>
                        </div>
                        <div class="mt-2 grid grid-cols-4 gap-1">
                            <button type="button" title="Copy URL" aria-label="Copy image URL" class="copy-media media-icon-action" data-path="<?php echo $this->escape((string) $item['path']); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="11" height="11" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg></button>
                            <button type="button" title="Replace image" aria-label="Replace image" class="replace-media media-icon-action text-blue-700" data-id="<?php echo (int) $item['id']; ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7h-6V1M20 7a9 9 0 1 0 1 8"/></svg></button>
                            <a href="<?php echo $this->escape((string) $item['path']); ?>" download title="Download image" aria-label="Download image" class="media-icon-action"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 19h16"/></svg></a>
                            <button type="button" title="Delete image" aria-label="Delete image" class="delete-media media-icon-action text-red-600" data-id="<?php echo (int) $item['id']; ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5"/></svg></button>
                        </div>
                        <input type="file" class="replacement-input sr-only" data-id="<?php echo (int) $item['id']; ?>" accept="image/jpeg,image/png,image/webp">
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <div id="media-empty" class="<?php echo $media === [] ? '' : 'hidden '; ?>rounded-2xl border-2 border-dashed border-slate-300 bg-white px-6 py-14 text-center"><h3 class="font-bold text-slate-800">No media found</h3><p class="mt-1 text-sm text-slate-500">Upload an image or adjust your search.</p></div>
        <div class="mt-5 text-center">
            <button type="button" id="load-more" class="<?php echo $hasMore ? '' : 'hidden '; ?>rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">Load more</button>
        </div>
    </section>

    <div id="media-drawer" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="drawer-title">
        <div id="drawer-backdrop" class="absolute inset-0 bg-slate-950/45 opacity-0 transition-opacity duration-200"></div>
        <aside id="drawer-panel" class="absolute inset-y-0 right-0 flex w-full max-w-xl translate-x-full flex-col bg-white shadow-2xl transition-transform duration-300">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-blue-700">Media details</p><h2 id="drawer-title" class="truncate text-lg font-bold text-slate-950">Loading…</h2></div>
                <button type="button" id="drawer-close" class="flex h-9 w-9 items-center justify-center rounded-xl text-2xl text-slate-500 hover:bg-slate-100" aria-label="Close media details">×</button>
            </header>
            <div id="drawer-loading" class="flex flex-1 items-center justify-center text-sm font-semibold text-blue-700" role="status">Loading media details…</div>
            <div id="drawer-content" class="hidden flex-1 overflow-y-auto">
                <div id="drawer-preview" class="flex min-h-64 items-center justify-center bg-slate-950"></div>
                <div class="space-y-5 p-5">
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 rounded-xl bg-slate-50 p-4 text-xs">
                        <div class="col-span-2"><dt class="font-bold text-slate-500">File name</dt><dd id="drawer-filename" class="mt-1 break-all text-slate-800"></dd></div>
                        <div><dt class="font-bold text-slate-500">Type</dt><dd id="drawer-type" class="mt-1 text-slate-800"></dd></div>
                        <div><dt class="font-bold text-slate-500">Size</dt><dd id="drawer-size" class="mt-1 text-slate-800"></dd></div>
                        <div><dt class="font-bold text-slate-500">Dimensions</dt><dd id="drawer-dimensions" class="mt-1 text-slate-800"></dd></div>
                        <div><dt class="font-bold text-slate-500">Uploaded</dt><dd id="drawer-date" class="mt-1 text-slate-800"></dd></div>
                        <div class="col-span-2"><dt class="font-bold text-slate-500">Storage path</dt><dd id="drawer-path" class="mt-1 break-all font-mono text-[11px] text-slate-800"></dd></div>
                        <div class="col-span-2"><dt class="font-bold text-slate-500">MIME type</dt><dd id="drawer-mime" class="mt-1 text-slate-800"></dd></div>
                    </dl>
                    <form id="drawer-form" class="grid gap-4">
                        <label><span class="text-sm font-bold text-slate-700">Title</span><input id="drawer-field-title" name="title" maxlength="255" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                        <label><span class="text-sm font-bold text-slate-700">Alt text</span><input id="drawer-alt" name="alt_text" maxlength="255" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm focus:border-blue-600 focus:ring-blue-600"><small class="mt-1 block text-xs text-slate-500">Describe the visual content for accessibility.</small></label>
                    </form>
                    <div id="drawer-error" role="alert" class="hidden rounded-xl bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                </div>
            </div>
            <footer id="drawer-actions" class="hidden border-t border-slate-200 bg-white p-4">
                <button type="button" id="drawer-save" class="w-full rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800 disabled:opacity-60">Save changes</button>
                <div class="mt-2 grid grid-cols-4 gap-2">
                    <button type="button" id="drawer-copy" class="rounded-xl bg-slate-100 px-2 py-2.5 text-xs font-bold text-slate-700" title="Copy media URL">Copy URL</button>
                    <a id="drawer-download" class="rounded-xl bg-slate-100 px-2 py-2.5 text-center text-xs font-bold text-slate-700" download title="Download media">Download</a>
                    <button type="button" id="drawer-replace" class="rounded-xl bg-blue-50 px-2 py-2.5 text-xs font-bold text-blue-700" title="Replace media">Replace</button>
                    <button type="button" id="drawer-delete" class="rounded-xl bg-red-50 px-2 py-2.5 text-xs font-bold text-red-600" title="Delete media">Delete</button>
                </div>
                <input type="file" id="drawer-replacement" accept="image/jpeg,image/png,image/webp" class="sr-only">
            </footer>
        </aside>
    </div>
</div>

<style>
.media-icon-action { display:flex; height:2.25rem; align-items:center; justify-content:center; border-radius:.6rem; color:#475569; transition:background-color .15s,color .15s,transform .15s; }
.media-icon-action:hover { background:#f1f5f9; color:#0f172a; transform:translateY(-1px); }
.media-icon-action:focus-visible { outline:2px solid #2563eb; outline-offset:2px; }
.media-icon-action svg { width:1.05rem; height:1.05rem; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
.media-filter { display:inline-flex; min-height:2.25rem; flex:none; align-items:center; gap:.4rem; border-radius:.55rem; padding:.4rem .7rem; font-size:.75rem; font-weight:700; color:#64748b; transition:background-color .15s,color .15s,box-shadow .15s; }
.media-filter:hover { color:#1e293b; }
.media-filter.is-active { background:#fff; color:#1d4ed8; box-shadow:0 1px 3px rgb(15 23 42 / .12); }
.media-filter:focus-visible { outline:2px solid #2563eb; outline-offset:1px; }
.media-filter-count { border-radius:999px; background:#e2e8f0; padding:.05rem .35rem; font-size:.65rem; color:#475569; }
.media-filter.is-active .media-filter-count { background:#dbeafe; color:#1d4ed8; }
</style>
<script>
(() => {
    const csrf = <?php echo json_encode($csrf, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const maxSize = 5 * 1024 * 1024;
    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
    const queue = new Map();
    let page = 1, hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>, searchTimer, activeCategory = 'all';
    const filesInput = document.getElementById('media-files');
    const dropzone = document.getElementById('media-dropzone');
    const queueNode = document.getElementById('upload-queue');
    const emptyQueue = document.getElementById('upload-empty');
    const actions = document.getElementById('upload-actions');
    const clearButton = document.getElementById('clear-queue');
    const alertNode = document.getElementById('upload-alert');
    const pageNotice = document.getElementById('page-notice');
    const modal = document.getElementById('upload-panel');
    const modalDialog = document.getElementById('upload-dialog');
    const modalBackdrop = document.getElementById('upload-backdrop');
    const grid = document.getElementById('media-grid');
    const loadMore = document.getElementById('load-more');
    const spinner = document.getElementById('library-spinner');
    const emptyLibrary = document.getElementById('media-empty');
    const drawer = document.getElementById('media-drawer');
    const drawerPanel = document.getElementById('drawer-panel');
    const drawerBackdrop = document.getElementById('drawer-backdrop');
    const detailsCache = new Map();
    let selectedMedia = null, drawerFocused = null;

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character]));
    const bytes = value => value >= 1048576 ? (value / 1048576).toFixed(1) + ' MB' : (Math.max(0, value) / 1024).toFixed(1) + ' KB';
    const date = value => value ? new Intl.DateTimeFormat(undefined, {year:'numeric', month:'short', day:'numeric'}).format(new Date(value.replace(' ', 'T'))) : '';
    const notice = (message, type = 'success', inModal = false) => {
        const target = inModal ? alertNode : pageNotice;
        target.textContent = message;
        target.className = (inModal ? 'mt-3 ' : 'fixed right-4 top-20 z-[60] max-w-sm shadow-lg ') + 'rounded-xl px-4 py-3 text-sm font-semibold ' + (type === 'error' ? 'bg-red-50 text-red-700 ring-1 ring-red-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200');
        window.setTimeout(() => target.classList.add('hidden'), 5000);
    };
    const card = item => {
        const preview = item.media_type === 'image'
            ? `<img src="${escapeHtml(item.path)}" alt="${escapeHtml(item.alt)}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy">`
            : `<span class="flex h-full w-full items-center justify-center text-4xl text-slate-400" aria-hidden="true">${item.media_type === 'video' ? '▷' : '▤'}</span>`;
        return `<article class="media-card group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" data-id="${item.id}">
        <button type="button" class="media-open relative block aspect-[4/3] w-full overflow-hidden bg-slate-100 text-left" data-id="${item.id}" aria-label="Open media details">${preview}<span class="absolute left-2 top-2 rounded-full bg-slate-950/75 px-2 py-1 text-[10px] font-bold uppercase text-white">${escapeHtml(item.mime_type.replace('image/', ''))}</span></button>
        <div class="p-2.5"><div class="flex items-center justify-between gap-2 text-[11px] text-slate-500"><span>${bytes(item.size)}</span><time>${date(item.created_at)}</time></div>
        <div class="mt-2 grid grid-cols-4 gap-1"><button type="button" title="Copy URL" aria-label="Copy media URL" class="copy-media media-icon-action" data-path="${escapeHtml(item.path)}"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="11" height="11" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg></button><button type="button" title="Replace media" aria-label="Replace media" class="replace-media media-icon-action text-blue-700" data-id="${item.id}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7h-6V1M20 7a9 9 0 1 0 1 8"/></svg></button><a href="${escapeHtml(item.path)}" download title="Download media" aria-label="Download media" class="media-icon-action"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 19h16"/></svg></a><button type="button" title="Delete media" aria-label="Delete media" class="delete-media media-icon-action text-red-600" data-id="${item.id}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5"/></svg></button></div><input type="file" class="replacement-input sr-only" data-id="${item.id}" accept="image/jpeg,image/png,image/webp"></div></article>`;
    };

    let previouslyFocused = null;
    const openModal = () => {
        previouslyFocused = document.activeElement;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalDialog.classList.remove('translate-y-4', 'scale-[.98]', 'opacity-0');
            dropzone.focus();
        });
    };
    const closeModal = () => {
        modalBackdrop.classList.add('opacity-0');
        modalDialog.classList.add('translate-y-4', 'scale-[.98]', 'opacity-0');
        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            if (previouslyFocused) previouslyFocused.focus();
        }, 200);
    };
    document.getElementById('show-uploader').addEventListener('click', openModal);
    document.querySelectorAll('[data-close-upload]').forEach(button => button.addEventListener('click', closeModal));
    modalBackdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });

    const showDrawerDetails = media => {
        selectedMedia = media;
        document.getElementById('drawer-title').textContent = media.title || media.name;
        document.getElementById('drawer-filename').textContent = media.name;
        document.getElementById('drawer-type').textContent = media.media_type.charAt(0).toUpperCase() + media.media_type.slice(1);
        document.getElementById('drawer-size').textContent = bytes(media.size);
        document.getElementById('drawer-dimensions').textContent = media.width && media.height ? `${media.width} × ${media.height} px` : 'Not available';
        document.getElementById('drawer-date').textContent = date(media.created_at);
        document.getElementById('drawer-path').textContent = media.path;
        document.getElementById('drawer-mime').textContent = media.mime_type;
        document.getElementById('drawer-field-title').value = media.title || '';
        document.getElementById('drawer-alt').value = media.alt_text || '';
        const preview = document.getElementById('drawer-preview');
        preview.innerHTML = media.media_type === 'image'
            ? `<img src="${escapeHtml(media.path)}" alt="${escapeHtml(media.alt)}" class="max-h-96 w-full object-contain">`
            : (media.media_type === 'video'
                ? `<video src="${escapeHtml(media.path)}" class="max-h-96 w-full" controls preload="metadata"></video>`
                : '<span class="text-7xl text-slate-400" aria-hidden="true">▤</span>');
        document.getElementById('drawer-download').href = media.path;
        document.getElementById('drawer-loading').classList.add('hidden');
        document.getElementById('drawer-content').classList.remove('hidden');
        document.getElementById('drawer-actions').classList.remove('hidden');
    };
    const openDrawer = async id => {
        drawerFocused = document.activeElement;
        drawer.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        document.getElementById('drawer-loading').classList.remove('hidden');
        document.getElementById('drawer-content').classList.add('hidden');
        document.getElementById('drawer-actions').classList.add('hidden');
        document.getElementById('drawer-error').classList.add('hidden');
        requestAnimationFrame(() => {
            drawerBackdrop.classList.remove('opacity-0');
            drawerPanel.classList.remove('translate-x-full');
            document.getElementById('drawer-close').focus();
        });
        if (detailsCache.has(id)) {
            showDrawerDetails(detailsCache.get(id));
            return;
        }
        try {
            const response = await fetch(`/admin/media/${id}/json`, {headers:{Accept:'application/json'}});
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Media details could not be loaded.');
            detailsCache.set(id, data.media);
            showDrawerDetails(data.media);
        } catch (error) {
            document.getElementById('drawer-loading').textContent = error.message;
        }
    };
    const closeDrawer = () => {
        drawerBackdrop.classList.add('opacity-0');
        drawerPanel.classList.add('translate-x-full');
        window.setTimeout(() => {
            drawer.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            if (drawerFocused) drawerFocused.focus();
        }, 300);
    };
    document.getElementById('drawer-close').addEventListener('click', closeDrawer);
    drawerBackdrop.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', event => { if (event.key === 'Escape' && !drawer.classList.contains('hidden')) closeDrawer(); });
    document.getElementById('drawer-save').addEventListener('click', async event => {
        if (!selectedMedia) return;
        const saveButton = event.currentTarget;
        saveButton.disabled = true;
        saveButton.textContent = 'Saving…';
        const body = new FormData(document.getElementById('drawer-form'));
        body.append('_token', csrf);
        try {
            const response = await fetch(`/admin/media/${selectedMedia.id}`, {method:'POST', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}, body});
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Changes could not be saved.');
            detailsCache.set(selectedMedia.id, data.media);
            showDrawerDetails(data.media);
            const image = grid.querySelector(`.media-card[data-id="${selectedMedia.id}"] img`);
            if (image) image.alt = data.media.alt;
            notice(data.message);
        } catch (error) {
            const errorNode = document.getElementById('drawer-error');
            errorNode.textContent = error.message;
            errorNode.classList.remove('hidden');
        }
        saveButton.disabled = false;
        saveButton.textContent = 'Save changes';
    });
    document.getElementById('drawer-copy').addEventListener('click', async () => {
        if (!selectedMedia) return;
        try { await navigator.clipboard.writeText(new URL(selectedMedia.path, window.location.origin).href); notice('Media URL copied.'); }
        catch (_) { notice('Could not copy the media URL.', 'error'); }
    });
    document.getElementById('drawer-replace').addEventListener('click', () => document.getElementById('drawer-replacement').click());
    document.getElementById('drawer-replacement').addEventListener('change', async event => {
        if (!selectedMedia || !event.target.files[0]) return;
        const error = validate(event.target.files[0]);
        if (error) { notice(error, 'error'); event.target.value = ''; return; }
        const body = new FormData();
        body.append('_token', csrf);
        body.append('image', event.target.files[0]);
        document.getElementById('drawer-replace').disabled = true;
        try {
            const response = await fetch(`/admin/media/${selectedMedia.id}/replace`, {method:'POST', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}, body});
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Replace failed.');
            const current = grid.querySelector(`.media-card[data-id="${selectedMedia.id}"]`);
            if (current) current.outerHTML = card(data.media);
            detailsCache.set(selectedMedia.id, data.media);
            showDrawerDetails(data.media);
            notice(data.message);
        } catch (replaceError) {
            notice(replaceError.message, 'error');
        }
        document.getElementById('drawer-replace').disabled = false;
        event.target.value = '';
    });
    document.getElementById('drawer-delete').addEventListener('click', async () => {
        if (!selectedMedia || !confirm('Delete this media item permanently? Existing pages using its URL may break.')) return;
        const body = new FormData(); body.append('_token', csrf);
        const response = await fetch(`/admin/media/${selectedMedia.id}/delete`, {method:'POST', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}, body});
        const data = await response.json();
        if (response.ok && data.success) {
            grid.querySelector(`.media-card[data-id="${selectedMedia.id}"]`)?.remove();
            detailsCache.delete(selectedMedia.id);
            closeDrawer();
            notice(data.message);
        } else notice(data.message || 'Delete failed.', 'error');
    });

    const validate = file => {
        if (!allowed.includes(file.type)) return `${file.name}: only JPG, PNG and WebP images are supported.`;
        if (file.size > maxSize) return `${file.name}: file must be 5 MB or smaller.`;
        return '';
    };
    const renderQueue = () => {
        queueNode.innerHTML = '';
        queue.forEach((entry, key) => {
            const item = document.createElement('article');
            item.className = 'overflow-hidden rounded-xl border border-slate-200 bg-white';
            item.innerHTML = `<div class="relative aspect-[16/9] bg-slate-100"><img src="${entry.preview}" alt="" class="h-full w-full object-cover"><button type="button" data-remove="${escapeHtml(key)}" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-slate-950/75 text-sm font-bold text-white" aria-label="Remove ${escapeHtml(entry.file.name)}">×</button></div><div class="p-2.5"><p class="truncate text-xs font-bold text-slate-800">${escapeHtml(entry.file.name)}</p><p class="mt-0.5 text-[11px] text-slate-500">${bytes(entry.file.size)}</p><div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100"><div data-progress="${escapeHtml(key)}" class="h-full bg-blue-600 transition-all" style="width:0"></div></div><p data-state="${escapeHtml(key)}" class="mt-1 text-[11px] text-slate-500">Ready</p></div>`;
            queueNode.appendChild(item);
        });
        const populated = queue.size > 0;
        emptyQueue.classList.toggle('hidden', populated);
        actions.classList.toggle('hidden', !populated);
        clearButton.classList.toggle('hidden', !populated);
    };
    const previewFor = file => new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(String(reader.result));
        reader.onerror = () => reject(new Error(`Could not preview ${file.name}.`));
        reader.readAsDataURL(file);
    });
    const addFiles = async fileList => {
        const errors = [];
        for (const file of Array.from(fileList)) {
            const error = validate(file);
            if (error) { errors.push(error); continue; }
            const key = `${file.name}-${file.size}-${file.lastModified}`;
            if (!queue.has(key)) {
                try { queue.set(key, {file, preview: await previewFor(file)}); }
                catch (previewError) { errors.push(previewError.message); }
            }
        }
        if (errors.length) notice(errors.join(' '), 'error', true);
        renderQueue();
    };
    const clearQueue = () => {
        queue.clear(); filesInput.value = ''; renderQueue();
    };
    dropzone.addEventListener('click', () => filesInput.click());
    dropzone.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); filesInput.click(); } });
    filesInput.addEventListener('change', () => addFiles(filesInput.files));
    ['dragenter', 'dragover'].forEach(type => dropzone.addEventListener(type, event => { event.preventDefault(); dropzone.classList.add('border-blue-500', 'bg-blue-50'); }));
    ['dragleave', 'drop'].forEach(type => dropzone.addEventListener(type, event => { event.preventDefault(); dropzone.classList.remove('border-blue-500', 'bg-blue-50'); }));
    dropzone.addEventListener('drop', event => addFiles(event.dataTransfer.files));
    clearButton.addEventListener('click', clearQueue);
    queueNode.addEventListener('click', event => {
        const button = event.target.closest('[data-remove]');
        if (!button) return;
        queue.delete(button.dataset.remove); renderQueue();
    });
    const uploadOne = (key, entry) => new Promise(resolve => {
        const form = new FormData();
        form.append('_token', csrf); form.append('images[]', entry.file);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/admin/media'); xhr.setRequestHeader('Accept', 'application/json'); xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.upload.onprogress = event => { if (event.lengthComputable) document.querySelector(`[data-progress="${CSS.escape(key)}"]`).style.width = Math.round(event.loaded / event.total * 100) + '%'; };
        xhr.onload = () => {
            let response = {}; try { response = JSON.parse(xhr.responseText); } catch (_) {}
            const success = xhr.status >= 200 && xhr.status < 300 && response.success;
            document.querySelector(`[data-state="${CSS.escape(key)}"]`).textContent = success ? 'Uploaded' : (response.errors?.[0] || response.message || 'Upload failed');
            resolve({key, success, message: response.message});
        };
        xhr.onerror = () => resolve({key, success:false, message:'Network error'});
        xhr.send(form);
    });
    document.getElementById('upload-button').addEventListener('click', async event => {
        if (!queue.size) return;
        const uploadButton = event.currentTarget;
        uploadButton.disabled = true;
        uploadButton.textContent = 'Uploading…';
        const results = [];
        for (const [key, entry] of queue) results.push(await uploadOne(key, entry));
        const succeeded = results.filter(result => result.success);
        succeeded.forEach(result => queue.delete(result.key));
        renderQueue();
        uploadButton.disabled = false;
        uploadButton.textContent = 'Upload selected';
        if (succeeded.length) {
            await fetchPage(1, true);
            notice(`${succeeded.length} image${succeeded.length === 1 ? '' : 's'} uploaded successfully.`);
            if (queue.size === 0) closeModal();
        } else notice(results[0]?.message || 'Upload failed.', 'error');
    });

    const fetchPage = async (requestedPage, reset = false) => {
        spinner.classList.remove('hidden'); loadMore.disabled = true;
        const query = document.getElementById('media-search').value.trim();
        try {
            const response = await fetch(`/admin/media/json?page=${requestedPage}&q=${encodeURIComponent(query)}&category=${encodeURIComponent(activeCategory)}`, {headers:{Accept:'application/json'}});
            const data = await response.json();
            if (reset) grid.innerHTML = '';
            data.media.forEach(item => grid.insertAdjacentHTML('beforeend', card(item)));
            page = data.page; hasMore = data.has_more; loadMore.classList.toggle('hidden', !hasMore);
            emptyLibrary.classList.toggle('hidden', data.total !== 0);
            document.getElementById('media-total').textContent = data.total;
            document.getElementById('library-status').textContent = `Showing ${Math.min(data.page * data.per_page, data.total)} of ${data.total} items`;
            Object.entries(data.counts || {}).forEach(([type, count]) => {
                const node = document.querySelector(`[data-count="${type}"]`);
                if (node) node.textContent = String(count);
            });
        } catch (_) { notice('The media library could not be loaded.', 'error'); }
        spinner.classList.add('hidden'); loadMore.disabled = false;
    };
    loadMore.addEventListener('click', () => fetchPage(page + 1));
    document.getElementById('media-filters').addEventListener('click', event => {
        const filter = event.target.closest('[data-category]');
        if (!filter || filter.dataset.category === activeCategory) return;
        activeCategory = filter.dataset.category;
        document.querySelectorAll('.media-filter').forEach(button => {
            const active = button === filter;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        const labels = {all:'All media', image:'Images', video:'Videos', document:'Documents'};
        document.getElementById('library-heading').textContent = labels[activeCategory];
        fetchPage(1, true);
    });
    document.getElementById('media-search').addEventListener('input', () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => fetchPage(1, true), 300); });

    grid.addEventListener('click', async event => {
        const opener = event.target.closest('.media-open');
        if (opener) {
            openDrawer(Number(opener.dataset.id));
            return;
        }
        const copy = event.target.closest('.copy-media');
        if (copy) {
            try { await navigator.clipboard.writeText(new URL(copy.dataset.path, window.location.origin).href); notice('Image URL copied.'); } catch (_) { notice('Could not copy the URL.', 'error'); }
            return;
        }
        const replace = event.target.closest('.replace-media');
        if (replace) { grid.querySelector(`.replacement-input[data-id="${replace.dataset.id}"]`).click(); return; }
        const remove = event.target.closest('.delete-media');
        if (!remove || !confirm('Delete this image permanently? Existing pages using its URL may show a broken image.')) return;
        remove.disabled = true;
        const body = new FormData(); body.append('_token', csrf);
        const response = await fetch(`/admin/media/${remove.dataset.id}/delete`, {method:'POST', headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}, body});
        const data = await response.json();
        if (response.ok && data.success) {
            grid.querySelector(`.media-card[data-id="${remove.dataset.id}"]`).remove();
            const totalNode = document.getElementById('media-total'); totalNode.textContent = String(Math.max(0, Number(totalNode.textContent) - 1));
            notice(data.message);
        } else { remove.disabled = false; notice(data.message || 'Delete failed.', 'error'); }
    });
    grid.addEventListener('change', event => {
        const input = event.target.closest('.replacement-input');
        if (!input || !input.files[0]) return;
        const error = validate(input.files[0]); if (error) { notice(error, 'error'); input.value = ''; return; }
        const body = new FormData(); body.append('_token', csrf); body.append('image', input.files[0]);
        const current = grid.querySelector(`.media-card[data-id="${input.dataset.id}"]`);
        current.classList.add('opacity-50', 'pointer-events-none');
        const xhr = new XMLHttpRequest(); xhr.open('POST', `/admin/media/${input.dataset.id}/replace`); xhr.setRequestHeader('Accept', 'application/json'); xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = () => {
            let data = {}; try { data = JSON.parse(xhr.responseText); } catch (_) {}
            if (xhr.status >= 200 && xhr.status < 300 && data.success) { detailsCache.delete(Number(input.dataset.id)); current.outerHTML = card(data.media); notice(data.message); }
            else { current.classList.remove('opacity-50', 'pointer-events-none'); notice(data.message || 'Replace failed.', 'error'); }
        };
        xhr.onerror = () => { current.classList.remove('opacity-50', 'pointer-events-none'); notice('Replace failed.', 'error'); };
        xhr.send(body);
    });
})();
</script>
