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
<div id="media-app" class="space-y-6" data-total="<?php echo (int) $total; ?>">
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950 p-5 text-white shadow-xl sm:p-7">
        <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-blue-500/20 blur-3xl"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-300">Asset management</p>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Media Library</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-300">
                    Browse, preview, and manage <span id="media-total" class="font-bold text-white"><?php echo (int) $total; ?></span> reusable website assets.
                </p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:flex-row lg:w-auto">
                <label class="relative block min-w-0 flex-1 lg:w-80">
                    <span class="sr-only">Search media</span>
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input id="media-search" type="search" value="<?php echo $this->escape($search); ?>" class="h-11 w-full rounded-xl border border-white/15 bg-white/10 pl-10 pr-3 text-sm text-white placeholder:text-slate-400 focus:border-blue-400 focus:ring-blue-400" placeholder="Search files, titles, or alt text">
                </label>
                <button type="button" id="show-uploader" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-500 px-5 text-sm font-bold text-white shadow-lg shadow-blue-950/20 transition hover:-translate-y-0.5 hover:bg-blue-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/></svg>
                    Upload media
                </button>
            </div>
        </div>
    </header>

    <section aria-labelledby="library-heading">
        <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 id="library-heading" class="text-lg font-bold text-slate-950">All media</h2>
                <p id="library-status" class="mt-0.5 text-xs text-slate-500" aria-live="polite">Showing <?php echo min(20, (int) $total); ?> of <?php echo (int) $total; ?> items</p>
            </div>
            <div id="media-filters" class="flex max-w-full gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1" aria-label="Filter media by type">
                <?php
                $filters = [
                    'all' => ['All', 'M4 6h16M4 12h16M4 18h16'],
                    'image' => ['Images', 'M4 5h16v14H4zM4 16l4-4 3 3 3-4 6 6'],
                    'video' => ['Videos', 'M5 4h11v16H5zM16 9l4-2v10l-4-2z'],
                    'document' => ['Documents', 'M6 3h9l4 4v14H6zM14 3v5h5M9 13h6M9 17h6'],
                ];
                foreach ($filters as $type => [$label, $icon]) :
                    $active = $type === 'all';
                    ?>
                    <button type="button" data-category="<?php echo $type; ?>" aria-pressed="<?php echo $active ? 'true' : 'false'; ?>" class="media-filter<?php echo $active ? ' is-active' : ''; ?>">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?php echo $icon; ?>"/></svg>
                        <?php echo $label; ?>
                        <span data-count="<?php echo $type; ?>" class="media-filter-count"><?php echo (int) ($categoryCounts[$type] ?? 0); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="media-grid" class="grid grid-cols-1 gap-4 min-[480px]:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
            <?php foreach ($media as $item) : ?>
                <article class="media-card group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg" data-id="<?php echo (int) $item['id']; ?>">
                    <button type="button" class="media-open relative block aspect-[4/3] w-full overflow-hidden bg-slate-100 text-left focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-blue-600" data-id="<?php echo (int) $item['id']; ?>" aria-label="Preview <?php echo $this->escape((string) ($item['title'] ?? $item['filename'] ?? 'media')); ?>">
                        <?php if (($item['media_type'] ?? 'image') === 'image') : ?>
                            <img src="<?php echo $this->escape((string) $item['path']); ?>" alt="<?php echo $this->escape((string) ($item['alt_text'] ?? $item['title'] ?? $item['filename'])); ?>" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">
                        <?php else : ?>
                            <span class="flex h-full w-full items-center justify-center text-slate-400" aria-hidden="true">
                                <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="<?php echo ($item['media_type'] ?? '') === 'video' ? 'M8 5v14l11-7z' : 'M6 3h9l4 4v14H6zM14 3v5h5'; ?>"/></svg>
                            </span>
                        <?php endif; ?>
                        <span class="absolute inset-0 flex items-center justify-center bg-slate-950/0 opacity-0 transition group-hover:bg-slate-950/30 group-hover:opacity-100" aria-hidden="true"><span class="rounded-full bg-white/95 p-3 text-slate-900 shadow-xl"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/><circle cx="12" cy="12" r="2.5"/></svg></span></span>
                        <span class="absolute left-2.5 top-2.5 rounded-full bg-slate-950/75 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white backdrop-blur"><?php echo $this->escape(str_replace(['image/', 'video/', 'application/'], '', (string) ($item['mime_type'] ?? 'file'))); ?></span>
                    </button>
                    <div class="p-3">
                        <div class="flex items-center justify-between gap-2 text-[11px] text-slate-500">
                            <span><?php echo $formatBytes((int) ($item['size'] ?? 0)); ?></span>
                            <time datetime="<?php echo $this->escape((string) ($item['created_at'] ?? '')); ?>"><?php echo !empty($item['created_at']) ? date('M j, Y', strtotime((string) $item['created_at'])) : ''; ?></time>
                        </div>
                        <div class="mt-3 grid grid-cols-4 gap-1 border-t border-slate-100 pt-2">
                            <button type="button" title="Copy URL" aria-label="Copy media URL" class="copy-media media-icon-action" data-path="<?php echo $this->escape((string) $item['path']); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="8" y="8" width="11" height="11" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg></button>
                            <button type="button" title="Replace media" aria-label="Replace media" class="replace-media media-icon-action" data-id="<?php echo (int) $item['id']; ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7h-6V1M20 7a9 9 0 1 0 1 8"/></svg></button>
                            <a href="<?php echo $this->escape((string) $item['path']); ?>" download title="Download media" aria-label="Download media" class="media-icon-action"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 19h16"/></svg></a>
                            <button type="button" title="Delete media" aria-label="Delete media" class="delete-media media-icon-action is-danger" data-id="<?php echo (int) $item['id']; ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5"/></svg></button>
                        </div>
                        <input type="file" class="replacement-input sr-only" data-id="<?php echo (int) $item['id']; ?>" accept="image/jpeg,image/png,image/webp">
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div id="media-empty" class="<?php echo $media === [] ? '' : 'hidden '; ?>rounded-3xl border-2 border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 5h16v14H4zM4 16l4-4 3 3 3-4 6 6"/></svg></span>
            <h3 class="mt-4 font-bold text-slate-800">No media found</h3>
            <p class="mt-1 text-sm text-slate-500">Upload a new asset or adjust your search and filters.</p>
        </div>

        <div id="infinite-sentinel" class="flex min-h-24 items-center justify-center" aria-live="polite">
            <div id="library-spinner" class="hidden items-center gap-3 text-sm font-semibold text-blue-700" role="status"><span class="media-spinner"></span>Loading more media…</div>
            <button type="button" id="load-more" class="<?php echo $hasMore ? '' : 'hidden '; ?>rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">Load more media</button>
            <div id="library-error" class="hidden text-center"><p class="text-sm text-red-700">More media could not be loaded.</p><button type="button" id="retry-load" class="mt-2 text-sm font-bold text-blue-700 hover:underline">Try again</button></div>
            <p id="library-end" class="<?php echo $hasMore || $total === 0 ? 'hidden ' : ''; ?>text-xs font-medium text-slate-400">You’ve reached the end of the library.</p>
        </div>
    </section>
</div>

<div id="page-notice" role="status" aria-live="polite" class="fixed right-4 top-20 z-[100] hidden max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-xl"></div>

<div id="upload-panel" class="media-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="upload-modal-title" aria-hidden="true">
    <div class="media-overlay-backdrop" data-close-upload></div>
    <section id="upload-dialog" class="media-modal max-w-4xl" tabindex="-1">
        <header class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
            <div><p class="text-xs font-bold uppercase tracking-wider text-blue-700">Add assets</p><h2 id="upload-modal-title" class="mt-1 text-lg font-bold text-slate-950">Upload media</h2><p class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP images. Maximum 5 MB per file.</p></div>
            <button type="button" data-close-upload class="media-close" aria-label="Close upload dialog">&times;</button>
        </header>
        <div class="max-h-[calc(100dvh-11rem)] overflow-y-auto p-5 sm:p-6">
            <div class="grid gap-5 lg:grid-cols-[minmax(16rem,.65fr)_minmax(0,1.35fr)]">
                <div>
                    <div id="media-dropzone" tabindex="0" role="button" aria-label="Select images to upload" class="flex min-h-52 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-5 py-7 text-center outline-none transition hover:border-blue-500 hover:bg-blue-50/60 focus-visible:ring-2 focus-visible:ring-blue-600">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/></svg></span>
                        <strong class="mt-3 text-sm text-slate-800">Drop images here</strong><span class="mt-1 text-xs text-slate-500">or click to browse your device</span>
                    </div>
                    <input id="media-files" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
                    <div id="upload-alert" role="alert" class="mt-3 hidden rounded-xl px-3 py-2 text-sm"></div>
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between"><p class="text-sm font-bold text-slate-700">Upload queue</p><button type="button" id="clear-queue" class="hidden rounded-lg px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50">Clear selected</button></div>
                    <div id="upload-empty" class="flex min-h-44 items-center justify-center rounded-xl border border-slate-200 px-5 text-center text-sm text-slate-400">Selected media will be summarized here.</div>
                    <div id="upload-queue" class="hidden min-h-44 items-center justify-center rounded-2xl border border-blue-200 bg-blue-50/60 p-6 text-center">
                        <div>
                            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-100 text-blue-700"><svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16v14H4zM4 16l4-4 3 3 3-4 6 6"/></svg></span>
                            <p id="upload-count" class="mt-4 text-lg font-bold text-slate-900"></p>
                            <p id="upload-total-size" class="mt-1 text-xs text-slate-500"></p>
                            <div id="upload-progress-wrap" class="mt-5 hidden" role="progressbar" aria-label="Upload progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="h-2.5 overflow-hidden rounded-full bg-blue-100">
                                    <div id="upload-progress" class="h-full rounded-full bg-blue-600 transition-[width] duration-300 ease-out" style="width:0%"></div>
                                </div>
                                <p id="upload-progress-label" class="mt-2 text-xs font-bold text-blue-700">Preparing upload…</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer id="upload-actions" class="hidden items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:px-6">
            <button type="button" data-close-upload class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-200">Cancel</button>
            <button type="button" id="upload-button" class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60">Upload selected</button>
        </footer>
    </section>
</div>

<div id="media-lightbox" class="media-overlay media-drawer-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="lightbox-title" aria-describedby="lightbox-description" aria-hidden="true">
    <div class="media-overlay-backdrop" data-close-lightbox></div>
    <section id="lightbox-dialog" class="media-lightbox-dialog" tabindex="-1">
        <header class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3 text-white sm:px-5">
            <div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-blue-300">Media details</p><h2 id="lightbox-title" class="mt-0.5 truncate text-sm font-bold">Loading…</h2></div>
            <button type="button" data-close-lightbox class="media-close text-white hover:bg-white/10" aria-label="Close media preview">&times;</button>
        </header>
        <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">
            <div id="lightbox-stage" class="relative flex min-h-64 items-center justify-center overflow-hidden bg-black/35 p-4 sm:min-h-80">
                <div id="lightbox-loading" class="flex items-center gap-3 text-sm font-semibold text-white" role="status"><span class="media-spinner border-white/25 border-t-white"></span>Loading preview…</div>
                <div id="lightbox-preview" class="hidden h-full w-full items-center justify-center"></div>
            </div>
            <aside class="border-t border-white/10 bg-white p-5">
                <p id="lightbox-description" class="sr-only">Preview and edit media details</p>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-xs">
                    <div class="col-span-2"><dt class="font-bold text-slate-500">File name</dt><dd id="lightbox-filename" class="mt-1 break-all text-slate-800"></dd></div>
                    <div><dt class="font-bold text-slate-500">Type</dt><dd id="lightbox-type" class="mt-1 text-slate-800"></dd></div>
                    <div><dt class="font-bold text-slate-500">Size</dt><dd id="lightbox-size" class="mt-1 text-slate-800"></dd></div>
                    <div><dt class="font-bold text-slate-500">Dimensions</dt><dd id="lightbox-dimensions" class="mt-1 text-slate-800"></dd></div>
                    <div><dt class="font-bold text-slate-500">Uploaded</dt><dd id="lightbox-date" class="mt-1 text-slate-800"></dd></div>
                </dl>
                <form id="lightbox-form" class="mt-5 grid gap-4 border-t border-slate-200 pt-5">
                    <label><span class="text-sm font-bold text-slate-700">Title</span><input id="lightbox-field-title" name="title" maxlength="255" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm focus:border-blue-600 focus:ring-blue-600"></label>
                    <label><span class="text-sm font-bold text-slate-700">Alt text</span><input id="lightbox-alt" name="alt_text" maxlength="255" class="mt-1 h-10 w-full rounded-xl border-slate-300 px-3 text-sm focus:border-blue-600 focus:ring-blue-600"><small class="mt-1 block text-xs text-slate-500">Describe the visual content for accessibility.</small></label>
                </form>
                <div id="lightbox-error" role="alert" class="mt-3 hidden rounded-xl bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                <button type="button" id="lightbox-save" class="mt-4 w-full rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-800 disabled:opacity-60">Save details</button>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    <button type="button" id="lightbox-copy" class="rounded-xl bg-slate-100 px-3 py-2.5 text-xs font-bold text-slate-700">Copy URL</button>
                    <a id="lightbox-download" class="rounded-xl bg-slate-100 px-3 py-2.5 text-center text-xs font-bold text-slate-700" download>Download</a>
                </div>
            </aside>
        </div>
    </section>
</div>

<style>
.media-icon-action{display:flex;height:2.25rem;align-items:center;justify-content:center;border-radius:.65rem;color:#475569;transition:background-color .15s,color .15s,transform .15s}
.media-icon-action:hover{background:#f1f5f9;color:#1d4ed8;transform:translateY(-1px)}
.media-icon-action.is-danger:hover{background:#fef2f2;color:#dc2626}
.media-icon-action:focus-visible{outline:2px solid #2563eb;outline-offset:2px}
.media-icon-action svg,.media-filter svg{width:1.05rem;height:1.05rem;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.media-filter{display:inline-flex;min-height:2.4rem;flex:none;align-items:center;gap:.4rem;border-radius:.6rem;padding:.45rem .75rem;font-size:.75rem;font-weight:700;color:#64748b;transition:background-color .15s,color .15s,box-shadow .15s}
.media-filter:hover{color:#1e293b}.media-filter.is-active{background:#fff;color:#1d4ed8;box-shadow:0 1px 3px rgb(15 23 42/.12)}
.media-filter:focus-visible{outline:2px solid #2563eb;outline-offset:1px}
.media-filter-count{border-radius:999px;background:#e2e8f0;padding:.05rem .35rem;font-size:.65rem;color:#475569}
.media-filter.is-active .media-filter-count{background:#dbeafe;color:#1d4ed8}
.media-overlay{position:fixed;inset:0;z-index:90;align-items:center;justify-content:center;padding:1rem}
.media-overlay.is-open{display:flex}.media-overlay-backdrop{position:absolute;inset:0;background:rgb(2 6 23/.72);backdrop-filter:blur(5px);opacity:0;transition:opacity .2s ease}
.media-modal{position:relative;z-index:1;width:100%;overflow:hidden;border-radius:1rem;background:#fff;box-shadow:0 25px 70px rgb(0 0 0/.35);opacity:0;transform:translateY(16px) scale(.98);transition:opacity .2s ease,transform .2s ease}
.media-overlay.is-visible .media-overlay-backdrop{opacity:1}.media-overlay.is-visible .media-modal{opacity:1;transform:none}
.media-close{display:flex;height:2.25rem;width:2.25rem;flex:none;align-items:center;justify-content:center;border-radius:.7rem;font-size:1.75rem;line-height:1;color:#64748b;transition:background-color .15s}
.media-close:hover{background:#f1f5f9}.media-close:focus-visible{outline:2px solid #60a5fa;outline-offset:2px}
.media-drawer-overlay{align-items:stretch;justify-content:flex-end;padding:0}
.media-lightbox-dialog{position:relative;z-index:1;display:flex;height:100dvh;width:min(34rem,100%);flex-direction:column;overflow:hidden;border-left:1px solid rgb(255 255 255/.12);background:#0f172a;box-shadow:-20px 0 60px rgb(0 0 0/.35);transform:translateX(100%);transition:transform .25s ease}
.media-overlay.is-visible .media-lightbox-dialog{transform:translateX(0)}
.media-spinner{display:inline-block;height:1.25rem;width:1.25rem;border:2px solid #bfdbfe;border-top-color:#2563eb;border-radius:999px;animation:media-spin .7s linear infinite}
@keyframes media-spin{to{transform:rotate(360deg)}}@media(prefers-reduced-motion:reduce){.media-modal,.media-lightbox-dialog,.media-overlay-backdrop,.media-card{transition:none!important}.media-spinner{animation-duration:1.5s}}
</style>

<script>
(() => {
    const csrf = <?php echo json_encode($csrf, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const app = document.getElementById('media-app');
    const grid = document.getElementById('media-grid');
    const modal = document.getElementById('upload-panel');
    const lightbox = document.getElementById('media-lightbox');
    const pageNotice = document.getElementById('page-notice');
    const spinner = document.getElementById('library-spinner');
    const loadMore = document.getElementById('load-more');
    const loadError = document.getElementById('library-error');
    const libraryEnd = document.getElementById('library-end');
    const emptyLibrary = document.getElementById('media-empty');
    const queue = new Map();
    const detailsCache = new Map();
    const loadedIds = new Set(Array.from(grid.querySelectorAll('.media-card'), card => Number(card.dataset.id)));
    let page = 1;
    let hasMore = <?php echo $hasMore ? 'true' : 'false'; ?>;
    let activeCategory = 'all';
    let isLoading = false;
    let listRequestId = 0;
    let searchTimer;
    let listController;
    let selectedMedia = null;
    let returnFocus = null;

    // Moving overlays to body prevents transformed dashboard ancestors from changing fixed positioning.
    [modal, lightbox, pageNotice].forEach(node => document.body.appendChild(node));

    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const bytes = value => value >= 1048576 ? `${(value / 1048576).toFixed(1)} MB` : `${(Math.max(0, value) / 1024).toFixed(1)} KB`;
    const date = value => value ? new Date(value.replace(' ', 'T')).toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'}) : '';
    const icon = type => type === 'video' ? '<svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 5v14l11-7z"/></svg>' : '<svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 3h9l4 4v14H6zM14 3v5h5"/></svg>';
    const notice = (message, type = 'success', upload = false) => {
        const node = upload ? document.getElementById('upload-alert') : pageNotice;
        node.textContent = message;
        node.className = `${upload ? 'mt-3 ' : 'fixed right-4 top-20 z-[100] max-w-sm shadow-xl '}rounded-xl px-4 py-3 text-sm font-semibold ${type === 'error' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-800'}`;
        clearTimeout(node._timer);
        if (!upload) node._timer = setTimeout(() => node.classList.add('hidden'), 4000);
    };
    const card = item => {
        const preview = item.media_type === 'image'
            ? `<img src="${escapeHtml(item.path)}" alt="${escapeHtml(item.alt)}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy" decoding="async">`
            : `<span class="flex h-full w-full items-center justify-center text-slate-400" aria-hidden="true">${icon(item.media_type)}</span>`;
        const title = item.title || item.name || 'Untitled';
        const type = String(item.mime_type || 'file').replace(/^(image|video|application)\//, '');
        return `<article class="media-card group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-blue-200 hover:shadow-lg" data-id="${item.id}">
            <button type="button" class="media-open relative block aspect-[4/3] w-full overflow-hidden bg-slate-100 text-left focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-blue-600" data-id="${item.id}" aria-label="Preview ${escapeHtml(title)}">${preview}<span class="absolute inset-0 flex items-center justify-center bg-slate-950/0 opacity-0 transition group-hover:bg-slate-950/30 group-hover:opacity-100" aria-hidden="true"><span class="rounded-full bg-white/95 p-3 text-slate-900 shadow-xl"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/><circle cx="12" cy="12" r="2.5"/></svg></span></span><span class="absolute left-2.5 top-2.5 rounded-full bg-slate-950/75 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-white backdrop-blur">${escapeHtml(type)}</span></button>
            <div class="p-3"><div class="flex items-center justify-between gap-2 text-[11px] text-slate-500"><span>${bytes(item.size)}</span><time datetime="${escapeHtml(item.created_at)}">${date(item.created_at)}</time></div>
            <div class="mt-3 grid grid-cols-4 gap-1 border-t border-slate-100 pt-2"><button type="button" title="Copy URL" aria-label="Copy media URL" class="copy-media media-icon-action" data-path="${escapeHtml(item.path)}"><svg viewBox="0 0 24 24"><rect x="8" y="8" width="11" height="11" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg></button><button type="button" title="Replace media" aria-label="Replace media" class="replace-media media-icon-action" data-id="${item.id}"><svg viewBox="0 0 24 24"><path d="M20 7h-6V1M20 7a9 9 0 1 0 1 8"/></svg></button><a href="${escapeHtml(item.path)}" download title="Download media" aria-label="Download media" class="media-icon-action"><svg viewBox="0 0 24 24"><path d="M12 3v12m0 0 4-4m-4 4-4-4M4 19h16"/></svg></a><button type="button" title="Delete media" aria-label="Delete media" class="delete-media media-icon-action is-danger" data-id="${item.id}"><svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m-9 0 1 13h10l1-13M10 11v5m4-5v5"/></svg></button></div><input type="file" class="replacement-input sr-only" data-id="${item.id}" accept="image/jpeg,image/png,image/webp"></div></article>`;
    };
    const focusable = root => Array.from(root.querySelectorAll('button:not([disabled]),a[href],input:not([disabled]),[tabindex]:not([tabindex="-1"])')).filter(node => node.offsetParent !== null);
    const trapFocus = (event, root) => {
        if (event.key !== 'Tab') return;
        const nodes = focusable(root);
        if (!nodes.length) return;
        if (event.shiftKey && document.activeElement === nodes[0]) { event.preventDefault(); nodes.at(-1).focus(); }
        else if (!event.shiftKey && document.activeElement === nodes.at(-1)) { event.preventDefault(); nodes[0].focus(); }
    };
    const openOverlay = (overlay, focusTarget) => {
        returnFocus = document.activeElement;
        overlay.classList.remove('hidden');
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        requestAnimationFrame(() => { overlay.classList.add('is-visible'); (focusTarget || overlay.querySelector('[tabindex="-1"]')).focus(); });
    };
    const closeOverlay = overlay => {
        overlay.classList.remove('is-visible');
        setTimeout(() => {
            overlay.classList.remove('is-open');
            overlay.classList.add('hidden');
            overlay.setAttribute('aria-hidden', 'true');
            if (!document.querySelector('.media-overlay.is-open')) document.body.classList.remove('overflow-hidden');
            returnFocus?.focus();
        }, 200);
    };

    document.getElementById('show-uploader').addEventListener('click', () => openOverlay(modal, document.getElementById('upload-dialog')));
    modal.querySelectorAll('[data-close-upload]').forEach(node => node.addEventListener('click', () => closeOverlay(modal)));
    lightbox.querySelectorAll('[data-close-lightbox]').forEach(node => node.addEventListener('click', () => closeOverlay(lightbox)));
    document.addEventListener('keydown', event => {
        const active = document.querySelector('.media-overlay.is-open');
        if (!active) return;
        if (event.key === 'Escape') { event.preventDefault(); closeOverlay(active); }
        else trapFocus(event, active);
    });

    const showLightbox = media => {
        selectedMedia = media;
        document.getElementById('lightbox-title').textContent = media.title || media.name || 'Media preview';
        document.getElementById('lightbox-filename').textContent = media.name;
        document.getElementById('lightbox-type').textContent = media.media_type;
        document.getElementById('lightbox-size').textContent = bytes(media.size);
        document.getElementById('lightbox-dimensions').textContent = media.width && media.height ? `${media.width} × ${media.height}px` : '—';
        document.getElementById('lightbox-date').textContent = date(media.created_at);
        document.getElementById('lightbox-field-title').value = media.title || '';
        document.getElementById('lightbox-alt').value = media.alt_text || '';
        document.getElementById('lightbox-download').href = media.path;
        const preview = document.getElementById('lightbox-preview');
        if (media.media_type === 'image') preview.innerHTML = `<img src="${escapeHtml(media.path)}" alt="${escapeHtml(media.alt)}" class="max-h-full max-w-full object-contain">`;
        else if (media.media_type === 'video') preview.innerHTML = `<video src="${escapeHtml(media.path)}" class="max-h-full max-w-full" controls preload="metadata"></video>`;
        else preview.innerHTML = `<div class="text-center text-white"><div class="mx-auto flex h-20 w-20 items-center justify-center">${icon(media.media_type)}</div><a href="${escapeHtml(media.path)}" target="_blank" rel="noopener" class="mt-4 inline-block rounded-xl bg-white px-4 py-2 text-sm font-bold text-slate-900">Open document</a></div>`;
        document.getElementById('lightbox-loading').classList.add('hidden');
        preview.classList.remove('hidden');
        preview.classList.add('flex');
    };
    const openLightbox = async id => {
        document.getElementById('lightbox-loading').classList.remove('hidden');
        const preview = document.getElementById('lightbox-preview');
        preview.classList.add('hidden');
        preview.classList.remove('flex');
        document.getElementById('lightbox-error').classList.add('hidden');
        openOverlay(lightbox, document.getElementById('lightbox-dialog'));
        if (detailsCache.has(id)) return showLightbox(detailsCache.get(id));
        try {
            const response = await fetch(`/admin/media/${id}/json`, {headers:{Accept:'application/json'}});
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Preview could not be loaded.');
            detailsCache.set(id, data.media);
            showLightbox(data.media);
        } catch (error) {
            document.getElementById('lightbox-loading').textContent = error.message;
        }
    };
    document.getElementById('lightbox-save').addEventListener('click', async event => {
        if (!selectedMedia) return;
        const button = event.currentTarget;
        button.disabled = true; button.textContent = 'Saving…';
        const body = new FormData(document.getElementById('lightbox-form')); body.append('_token', csrf);
        try {
            const response = await fetch(`/admin/media/${selectedMedia.id}`, {method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body});
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Changes could not be saved.');
            detailsCache.set(selectedMedia.id, data.media); showLightbox(data.media);
            const image = grid.querySelector(`.media-card[data-id="${selectedMedia.id}"] img`); if (image) image.alt = data.media.alt;
            notice(data.message);
        } catch (error) {
            const node = document.getElementById('lightbox-error'); node.textContent = error.message; node.classList.remove('hidden');
        } finally { button.disabled = false; button.textContent = 'Save details'; }
    });
    document.getElementById('lightbox-copy').addEventListener('click', async () => {
        if (!selectedMedia) return;
        try { await navigator.clipboard.writeText(new URL(selectedMedia.path, location.origin).href); notice('Media URL copied.'); } catch (_) { notice('Could not copy the URL.', 'error'); }
    });

    const filesInput = document.getElementById('media-files');
    const dropzone = document.getElementById('media-dropzone');
    const queueNode = document.getElementById('upload-queue');
    const uploadProgressWrap = document.getElementById('upload-progress-wrap');
    const uploadProgress = document.getElementById('upload-progress');
    const uploadProgressLabel = document.getElementById('upload-progress-label');
    const validate = file => !['image/jpeg','image/png','image/webp'].includes(file.type) ? `${file.name}: unsupported file type.` : file.size > 5 * 1024 * 1024 ? `${file.name}: file must be 5 MB or smaller.` : '';
    const renderQueue = () => {
        const populated = queue.size > 0;
        const totalSize = Array.from(queue.values()).reduce((sum, entry) => sum + entry.file.size, 0);
        document.getElementById('upload-count').textContent = `${queue.size} media file${queue.size === 1 ? '' : 's'} selected`;
        document.getElementById('upload-total-size').textContent = `${bytes(totalSize)} total`;
        document.getElementById('upload-empty').classList.toggle('hidden', populated);
        queueNode.classList.toggle('hidden', !populated);
        queueNode.classList.toggle('flex', populated);
        uploadProgressWrap.classList.add('hidden');
        uploadProgress.style.width = '0%';
        uploadProgressWrap.setAttribute('aria-valuenow', '0');
        document.getElementById('upload-actions').classList.toggle('hidden', queue.size === 0);
        document.getElementById('upload-actions').classList.toggle('flex', queue.size > 0);
        document.getElementById('clear-queue').classList.toggle('hidden', queue.size === 0);
    };
    const addFiles = async files => {
        const errors = [];
        for (const file of files) {
            const error = validate(file); if (error) { errors.push(error); continue; }
            const key = `${file.name}-${file.size}-${file.lastModified}`;
            if (!queue.has(key)) queue.set(key, {file});
        }
        if (errors.length) notice(errors.join(' '), 'error', true);
        renderQueue();
    };
    dropzone.addEventListener('click', () => filesInput.click());
    dropzone.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); filesInput.click(); } });
    filesInput.addEventListener('change', () => addFiles(filesInput.files));
    ['dragenter','dragover'].forEach(type => dropzone.addEventListener(type, event => { event.preventDefault(); dropzone.classList.add('border-blue-500','bg-blue-50'); }));
    ['dragleave','drop'].forEach(type => dropzone.addEventListener(type, event => { event.preventDefault(); dropzone.classList.remove('border-blue-500','bg-blue-50'); }));
    dropzone.addEventListener('drop', event => addFiles(event.dataTransfer.files));
    document.getElementById('clear-queue').addEventListener('click', () => { queue.clear(); filesInput.value=''; renderQueue(); });
    const uploadOne = (key, entry, completed, total) => new Promise(resolve => {
        const form = new FormData(); form.append('_token', csrf); form.append('images[]', entry.file);
        const xhr = new XMLHttpRequest(); xhr.open('POST','/admin/media'); xhr.setRequestHeader('Accept','application/json'); xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
        xhr.upload.onprogress = event => {
            if (!event.lengthComputable) return;
            const percent = Math.round(((completed + (event.loaded / event.total)) / total) * 100);
            uploadProgress.style.width = `${percent}%`;
            uploadProgressWrap.setAttribute('aria-valuenow', String(percent));
            uploadProgressLabel.textContent = `Uploading ${completed + 1} of ${total} · ${percent}%`;
        };
        xhr.onload = () => { let data={}; try{data=JSON.parse(xhr.responseText)}catch(_){} const success=xhr.status>=200&&xhr.status<300&&data.success; resolve({key,success}); };
        xhr.onerror = () => resolve({key,success:false}); xhr.send(form);
    });
    document.getElementById('upload-button').addEventListener('click', async event => {
        if (!queue.size) return;
        const button=event.currentTarget; button.disabled=true; button.textContent='Uploading…';
        uploadProgressWrap.classList.remove('hidden');
        uploadProgressLabel.textContent = `Uploading 1 of ${queue.size} · 0%`;
        const entries = Array.from(queue.entries());
        const results=[];
        for (let index = 0; index < entries.length; index++) {
            results.push(await uploadOne(entries[index][0], entries[index][1], index, entries.length));
        }
        const succeeded=results.filter(result=>result.success); succeeded.forEach(result=>queue.delete(result.key));
        uploadProgress.style.width = '100%';
        uploadProgressWrap.setAttribute('aria-valuenow', '100');
        uploadProgressLabel.textContent = `Upload complete · ${succeeded.length} of ${entries.length} successful`;
        button.disabled=false; button.textContent='Upload selected';
        if (succeeded.length) {
            await fetchPage(1, true);
            notice(`${succeeded.length} image${succeeded.length === 1 ? '' : 's'} uploaded successfully.`);
            if (!queue.size) {
                closeOverlay(modal);
            } else {
                const failed = entries.length - succeeded.length;
                renderQueue();
                notice(`${failed} file${failed === 1 ? '' : 's'} could not be uploaded. Please try again.`, 'error', true);
            }
        } else {
            notice('Upload failed. Please try again.', 'error', true);
        }
    });

    const updateLoadingUi = loading => {
        spinner.classList.toggle('hidden', !loading);
        spinner.classList.toggle('flex', loading);
        loadMore.classList.toggle('hidden', loading || !hasMore);
    };
    const fetchPage = async (requestedPage, reset = false) => {
        if (isLoading && !reset) return;
        if (reset) listController?.abort();
        const requestId = ++listRequestId;
        listController = new AbortController(); isLoading = true; loadError.classList.add('hidden'); updateLoadingUi(true);
        const query = document.getElementById('media-search').value.trim();
        try {
            const response = await fetch(`/admin/media/json?page=${requestedPage}&q=${encodeURIComponent(query)}&category=${encodeURIComponent(activeCategory)}`, {headers:{Accept:'application/json'},signal:listController.signal});
            if (!response.ok) throw new Error();
            const data = await response.json();
            if (reset) { grid.innerHTML=''; loadedIds.clear(); }
            data.media.forEach(item => { if (!loadedIds.has(item.id)) { loadedIds.add(item.id); grid.insertAdjacentHTML('beforeend',card(item)); } });
            page=data.page; hasMore=data.has_more;
            emptyLibrary.classList.toggle('hidden',data.total!==0);
            libraryEnd.classList.toggle('hidden',hasMore||data.total===0);
            document.getElementById('media-total').textContent=data.total;
            document.getElementById('library-status').textContent=`Showing ${loadedIds.size} of ${data.total} items`;
            Object.entries(data.counts||{}).forEach(([type,count])=>{const node=document.querySelector(`[data-count="${type}"]`);if(node)node.textContent=count});
        } catch (error) {
            if(error.name!=='AbortError'){loadError.classList.remove('hidden');notice('The media library could not be loaded.','error')}
        } finally {
            if (requestId === listRequestId) {
                isLoading = false;
                updateLoadingUi(false);
            }
        }
    };
    loadMore.addEventListener('click',()=>fetchPage(page+1));
    document.getElementById('retry-load').addEventListener('click',()=>fetchPage(page+1));
    new IntersectionObserver(entries=>{if(entries[0].isIntersecting&&hasMore&&!isLoading)fetchPage(page+1)},{rootMargin:'500px 0px'}).observe(document.getElementById('infinite-sentinel'));
    document.getElementById('media-filters').addEventListener('click',event=>{
        const filter=event.target.closest('[data-category]');if(!filter||filter.dataset.category===activeCategory)return;
        activeCategory=filter.dataset.category;document.querySelectorAll('.media-filter').forEach(button=>{const active=button===filter;button.classList.toggle('is-active',active);button.setAttribute('aria-pressed',active)});
        document.getElementById('library-heading').textContent={all:'All media',image:'Images',video:'Videos',document:'Documents'}[activeCategory];fetchPage(1,true);
    });
    document.getElementById('media-search').addEventListener('input',()=>{clearTimeout(searchTimer);searchTimer=setTimeout(()=>fetchPage(1,true),300)});

    const replaceMedia = async (id,file,cardNode) => {
        const error=validate(file);if(error){notice(error,'error');return}
        const body=new FormData();body.append('_token',csrf);body.append('image',file);cardNode?.classList.add('opacity-50','pointer-events-none');
        try{const response=await fetch(`/admin/media/${id}/replace`,{method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body});const data=await response.json();if(!response.ok||!data.success)throw new Error(data.message||'Replace failed.');cardNode.outerHTML=card(data.media);detailsCache.set(id,data.media);notice(data.message)}
        catch(error){cardNode?.classList.remove('opacity-50','pointer-events-none');notice(error.message,'error')}
    };
    grid.addEventListener('click',async event=>{
        const opener=event.target.closest('.media-open');if(opener){openLightbox(Number(opener.dataset.id));return}
        const copy=event.target.closest('.copy-media');if(copy){try{await navigator.clipboard.writeText(new URL(copy.dataset.path,location.origin).href);notice('Media URL copied.')}catch(_){notice('Could not copy the URL.','error')}return}
        const replace=event.target.closest('.replace-media');if(replace){grid.querySelector(`.replacement-input[data-id="${replace.dataset.id}"]`)?.click();return}
        const remove=event.target.closest('.delete-media');if(!remove||!confirm('Delete this media item permanently? Existing pages using its URL may break.'))return;
        remove.disabled=true;const body=new FormData();body.append('_token',csrf);
        try{const response=await fetch(`/admin/media/${remove.dataset.id}/delete`,{method:'POST',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},body});const data=await response.json();if(!response.ok||!data.success)throw new Error(data.message||'Delete failed.');grid.querySelector(`.media-card[data-id="${remove.dataset.id}"]`)?.remove();loadedIds.delete(Number(remove.dataset.id));document.getElementById('media-total').textContent=Math.max(0,Number(document.getElementById('media-total').textContent)-1);notice(data.message)}catch(error){remove.disabled=false;notice(error.message,'error')}
    });
    grid.addEventListener('change',event=>{const input=event.target.closest('.replacement-input');if(input?.files[0])replaceMedia(Number(input.dataset.id),input.files[0],input.closest('.media-card'))});
})();
</script>
