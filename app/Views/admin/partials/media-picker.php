<?php
/**
 * Reusable media-picker modal.
 *
 * Usage: echo $this->partial('admin/partials/media-picker', [
 *     'fieldId'   => 'featured_image',      // hidden input id
 *     'fieldName' => 'featured_image',      // hidden input name
 *     'previewId' => 'featured_img_preview',// img preview element id
 *     'triggerId' => 'pick_featured',       // button id that opens the modal
 * ]);
 *
 * Each consumer must already have an <input type="hidden" id="{fieldId}" name="{fieldName}">
 * and an <img id="{previewId}"> in their form. This partial only renders the modal + JS once
 * per page (guarded by a PHP static flag).
 */

static $mediaPickerRendered = false;
if ($mediaPickerRendered) {
    return;
}
$mediaPickerRendered = true;
?>

<!-- ── Media Picker Modal ────────────────────────────────────────────────── -->
<div
    id="media-picker-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4"
    aria-modal="true"
    role="dialog"
    aria-label="Media Library"
>
    <div class="flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-bold text-gray-900">Select Image</h2>
            <div class="flex items-center gap-3">
                <input
                    id="media-picker-search"
                    type="search"
                    placeholder="Search images…"
                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                >
                <button type="button" id="media-picker-close" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900" aria-label="Close">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div id="media-picker-filters" class="flex gap-1 overflow-x-auto border-b border-gray-200 bg-gray-50 px-5 py-2">
            <button type="button" data-category="all" class="media-picker-filter rounded-lg bg-blue-700 px-3 py-1.5 text-xs font-bold text-white">All</button>
            <button type="button" data-category="image" class="media-picker-filter rounded-lg px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-white">Images</button>
            <button type="button" data-category="video" class="media-picker-filter rounded-lg px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-white">Videos</button>
            <button type="button" data-category="document" class="media-picker-filter rounded-lg px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-white">Documents</button>
        </div>

        <!-- Grid -->
        <div id="media-picker-grid" class="grid h-[58vh] flex-none auto-rows-[9.5rem] grid-cols-2 content-start gap-3 overflow-y-auto p-5 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <div id="media-picker-empty" class="col-span-full py-16 text-center text-sm text-gray-400 hidden">No images found.</div>
            <div id="media-picker-loading" class="col-span-full py-16 text-center text-sm text-gray-400">Loading…</div>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4">
            <a href="/admin/media" target="_blank" class="text-sm font-medium text-blue-700 hover:underline">Upload new image ↗</a>
            <div class="flex items-center gap-3">
                <button type="button" id="media-picker-more" class="hidden rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Load more</button>
                <button type="button" id="media-picker-confirm" class="hidden rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 disabled:opacity-50">Add 0 images</button>
                <button type="button" id="media-picker-close-btn" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modal      = document.getElementById('media-picker-modal');
    const grid       = document.getElementById('media-picker-grid');
    const search     = document.getElementById('media-picker-search');
    const empty      = document.getElementById('media-picker-empty');
    const loading    = document.getElementById('media-picker-loading');
    const confirmBtn = document.getElementById('media-picker-confirm');
    const moreBtn    = document.getElementById('media-picker-more');
    const filters    = document.getElementById('media-picker-filters');
    const modalTitle = modal.querySelector('h2');

    let activeFieldId   = null;
    let activePreviewId = null;
    let searchTimer     = null;
    let multiMode       = false;
    let multiCallback   = null;
    let selectionCallback = null;
    let allowedTypes    = ['image'];
    let activeCategory  = 'all';
    let currentPage     = 1;
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character]));
    const selected      = new Map(); // path → alt

    function open(fieldId, previewId) {
        activeFieldId   = fieldId;
        activePreviewId = previewId;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        window.dispatchEvent(new CustomEvent('media-picker:opened'));
        search.value = '';
        activeCategory = 'all';
        updateFilters();
        loadMedia('', 1, true);
        search.focus();
    }

    function close() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        activeFieldId   = null;
        activePreviewId = null;
        multiMode       = false;
        multiCallback   = null;
        selectionCallback = null;
        allowedTypes = ['image'];
        selected.clear();
        confirmBtn.classList.add('hidden');
        modalTitle.textContent = 'Select Image';
        window.dispatchEvent(new CustomEvent('media-picker:closed'));
    }

    function loadMedia(q, requestedPage = 1, reset = true) {
        loading.classList.remove('hidden');
        empty.classList.add('hidden');
        if (reset) {
            Array.from(grid.children).forEach(el => {
                if (el !== empty && el !== loading) el.remove();
            });
        }

        const url = `/admin/media/json?page=${requestedPage}&q=${encodeURIComponent(q)}&category=${encodeURIComponent(activeCategory)}`;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                loading.classList.add('hidden');
                const items = data.media || [];
                currentPage = data.page || requestedPage;
                moreBtn.classList.toggle('hidden', !data.has_more);
                if (items.length === 0 && reset) { empty.classList.remove('hidden'); return; }
                items.forEach(item => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.dataset.path = item.path;
                    btn.dataset.alt  = item.alt;
                    const isSelected = multiMode && selected.has(item.path);
                    const selectable = allowedTypes.includes(item.media_type);
                    btn.disabled = !selectable;
                    btn.className = 'group relative h-[9.5rem] min-h-[9.5rem] overflow-hidden rounded-lg border-2 bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 ' +
                        (!selectable ? 'cursor-not-allowed opacity-45 ' : '') +
                        (isSelected ? 'border-blue-600' : 'border-transparent hover:border-blue-500');
                    const preview = item.media_type === 'image'
                        ? `<img src="${escapeHtml(item.path)}" alt="${escapeHtml(item.alt)}" class="h-28 w-full object-cover transition group-hover:opacity-90">`
                        : `<span class="flex h-28 items-center justify-center text-4xl text-gray-400">${item.media_type === 'video' ? '▷' : '▤'}</span>`;
                    btn.innerHTML = `
                        ${preview}
                        <span class="check-mark absolute right-1.5 top-1.5 rounded-full bg-blue-600 p-0.5 text-white ${isSelected ? '' : 'hidden'}">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <p class="truncate px-2 py-1.5 text-xs text-gray-600">${escapeHtml(item.name)}</p>
                    `;
                    btn.addEventListener('click', () => pick(item, btn));
                    grid.insertBefore(btn, loading);
                });
            })
            .catch(() => {
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
            });
    }

    function updateConfirmLabel() {
        const n = selected.size;
        confirmBtn.textContent = n === 1 ? 'Add 1 image' : `Add ${n} images`;
        confirmBtn.disabled = n === 0;
    }

    function pick(item, btn) {
        const path = item.path;
        const alt = item.alt;
        if (multiMode) {
            if (selected.has(path)) {
                selected.delete(path);
                btn.classList.remove('border-blue-600');
                btn.classList.add('border-transparent', 'hover:border-blue-500');
                btn.querySelector('.check-mark').classList.add('hidden');
            } else {
                selected.set(path, alt);
                btn.classList.add('border-blue-600');
                btn.classList.remove('border-transparent', 'hover:border-blue-500');
                btn.querySelector('.check-mark').classList.remove('hidden');
            }
            updateConfirmLabel();
            return;
        }

        if (selectionCallback) {
            selectionCallback(item);
            close();
            return;
        }

        if (activeFieldId) {
            const input = document.getElementById(activeFieldId);
            if (input) input.value = path;
        }
        if (activePreviewId) {
            const img = document.getElementById(activePreviewId);
            if (img) { img.src = path; img.alt = alt; img.classList.remove('hidden'); }
        }
        close();
    }

    confirmBtn.addEventListener('click', () => {
        if (multiCallback && selected.size > 0) {
            multiCallback(Array.from(selected.entries()).map(([path, alt]) => ({ path, alt })));
        }
        close();
    });

    search.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadMedia(search.value.trim(), 1, true), 300);
    });

    function updateFilters() {
        filters.querySelectorAll('[data-category]').forEach(button => {
            const active = button.dataset.category === activeCategory;
            button.classList.toggle('bg-blue-700', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('text-gray-600', !active);
        });
    }
    filters.addEventListener('click', event => {
        const button = event.target.closest('[data-category]');
        if (!button || button.dataset.category === activeCategory) return;
        activeCategory = button.dataset.category;
        updateFilters();
        loadMedia(search.value.trim(), 1, true);
    });
    moreBtn.addEventListener('click', () => loadMedia(search.value.trim(), currentPage + 1, false));

    document.getElementById('media-picker-close').addEventListener('click', close);
    document.getElementById('media-picker-close-btn').addEventListener('click', close);
    modal.addEventListener('click', e => { if (e.target === modal) close(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });

    window.openMediaPicker = open;

    window.openMediaPickerWithCallback = function (callback, types = ['image']) {
        selectionCallback = callback;
        allowedTypes = types;
        modalTitle.textContent = 'Select Media';
        open(null, null);
    };

    window.openMediaPickerMulti = function (callback) {
        multiMode     = true;
        multiCallback = callback;
        selected.clear();
        modalTitle.textContent = 'Select Images';
        updateConfirmLabel();
        confirmBtn.classList.remove('hidden');
        open(null, null);
    };
})();
</script>
