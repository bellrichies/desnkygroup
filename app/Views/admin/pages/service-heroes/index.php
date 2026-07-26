<?php $serviceId = (int) $service['id']; ?>
<div class="space-y-5">
    <header class="flex flex-col gap-4 rounded-2xl bg-gradient-to-r from-slate-950 to-blue-950 p-5 text-white shadow-lg sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-200">Service hero manager</p>
            <h1 class="mt-2 text-2xl font-bold"><?php echo $this->escape((string) $service['title']); ?></h1>
            <p class="mt-1 text-sm text-slate-300">Create, order and publish the hero slides shown on this service page.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/services/<?php echo $this->escape((string) $service['slug']); ?>" target="_blank" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10">Preview page</a>
            <a href="/admin/services/<?php echo $serviceId; ?>/heroes/create" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-bold text-white hover:bg-blue-400">+ Add slide</a>
        </div>
    </header>

    <?php if ($heroes === []) : ?>
        <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-white px-6 py-14 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-2xl text-blue-700">✦</div>
            <h2 class="mt-4 text-lg font-bold text-slate-900">No hero slides yet</h2>
            <p class="mt-1 text-sm text-slate-500">The service page will use its safe database fallback until you add a slide.</p>
            <a href="/admin/services/<?php echo $serviceId; ?>/heroes/create" class="mt-5 inline-flex rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Create first slide</a>
        </div>
    <?php else : ?>
        <form method="POST" action="/admin/services/<?php echo $serviceId; ?>/heroes/reorder" class="space-y-3">
            <?php echo $this->csrfField(); ?>
            <div class="flex items-center justify-between">
                <p class="text-sm text-slate-500"><?php echo count($heroes); ?> slide<?php echo count($heroes) === 1 ? '' : 's'; ?> · lower order appears first</p>
                <button class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Save order</button>
            </div>
            <div class="grid gap-3">
                <?php foreach ($heroes as $hero) : ?>
                    <article class="grid overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md md:grid-cols-[12rem_1fr_auto]">
                        <div class="relative min-h-32 bg-slate-900">
                            <?php if (($hero['media_type'] ?? 'image') === 'video' && !empty($hero['background_media'])) : ?>
                                <video src="<?php echo $this->escape((string) $hero['background_media']); ?>" class="absolute inset-0 h-full w-full object-cover" muted preload="metadata"></video>
                            <?php elseif (!empty($hero['background_media'])) : ?>
                                <img src="<?php echo $this->escape((string) $hero['background_media']); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <span class="absolute bottom-3 left-3 rounded-full bg-black/60 px-2 py-1 text-xs font-bold uppercase text-white"><?php echo $this->escape((string) ($hero['media_type'] ?? 'image')); ?></span>
                        </div>
                        <div class="min-w-0 p-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-lg font-bold text-slate-900"><?php echo $this->escape((string) $hero['heading']); ?></h2>
                                <span class="rounded-full px-2 py-1 text-xs font-bold <?php echo !empty($hero['is_active']) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'; ?>"><?php echo !empty($hero['is_active']) ? 'Enabled' : 'Disabled'; ?></span>
                                <?php if (empty($hero['is_visible'])) : ?><span class="rounded-full bg-amber-100 px-2 py-1 text-xs font-bold text-amber-700">Hidden</span><?php endif; ?>
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm text-slate-500"><?php echo $this->escape((string) ($hero['subheading'] ?? $hero['description'] ?? 'No supporting text')); ?></p>
                            <label class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-slate-500">
                                Order
                                <input type="number" min="0" name="orders[<?php echo (int) $hero['id']; ?>]" value="<?php echo (int) $hero['sort_order']; ?>" class="h-9 w-20 rounded-lg border-slate-300 text-sm focus:border-blue-600 focus:ring-blue-600">
                            </label>
                        </div>
                        <div class="flex items-center gap-2 border-t border-slate-100 p-4 md:flex-col md:justify-center md:border-l md:border-t-0">
                            <a href="/admin/services/<?php echo $serviceId; ?>/heroes/<?php echo (int) $hero['id']; ?>/edit" class="w-full rounded-lg bg-blue-700 px-3 py-2 text-center text-sm font-bold text-white hover:bg-blue-800">Edit</a>
                            <button form="toggle-<?php echo (int) $hero['id']; ?>" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700"><?php echo !empty($hero['is_active']) ? 'Disable' : 'Enable'; ?></button>
                            <button form="delete-<?php echo (int) $hero['id']; ?>" class="w-full rounded-lg px-3 py-2 text-sm font-bold text-red-600 hover:bg-red-50">Delete</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </form>
        <?php foreach ($heroes as $hero) : ?>
            <form id="toggle-<?php echo (int) $hero['id']; ?>" method="POST" action="/admin/services/<?php echo $serviceId; ?>/heroes/<?php echo (int) $hero['id']; ?>/toggle"><?php echo $this->csrfField(); ?></form>
            <form id="delete-<?php echo (int) $hero['id']; ?>" method="POST" action="/admin/services/<?php echo $serviceId; ?>/heroes/<?php echo (int) $hero['id']; ?>/delete" onsubmit="return confirm('Delete this hero slide permanently?')"><?php echo $this->csrfField(); ?></form>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
