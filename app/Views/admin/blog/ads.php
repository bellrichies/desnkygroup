<?php
$placements = $placements ?? [];
$csrf = (string) ($csrf_token ?? '');
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Blog Advertisements</h1>
        <p class="mt-1 text-sm text-gray-600">Configure approved AdSense placements with reserved dimensions and consent-aware loading.</p>
    </div>
    <a href="/admin/blog" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to posts</a>
</div>

<div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900">
    Ads are disabled by default. Keep placements sparse, clearly labelled, and outside controls, login pages, previews, drafts, errors, or low-content pages. Update <code>public/ads.txt</code> with the verified publisher line after AdSense approval.
</div>

<div class="grid gap-5">
    <?php foreach ($placements as $placement) : ?>
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <form method="post" action="/admin/blog/ads/<?php echo (int) $placement['id']; ?>" class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_16rem]">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                <input type="hidden" name="is_enabled" value="0">
                <div class="grid gap-3">
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Placement</span>
                            <input name="label" required value="<?php echo $this->escape((string) $placement['label']); ?>" class="mt-1 w-full rounded border-gray-300 font-semibold">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Key</span>
                            <input value="<?php echo $this->escape((string) $placement['placement_key']); ?>" disabled class="mt-1 w-full rounded border-gray-200 bg-gray-50 font-mono text-sm text-gray-500">
                        </label>
                    </div>
                    <label class="block">
                        <span class="text-xs font-semibold text-gray-600">Description</span>
                        <input name="description" value="<?php echo $this->escape((string) ($placement['description'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300">
                    </label>
                    <div class="grid gap-3 md:grid-cols-2">
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">AdSense client</span>
                            <input name="adsense_client" value="<?php echo $this->escape((string) ($placement['adsense_client'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 font-mono text-sm" placeholder="ca-pub-0000000000000000">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Ad slot</span>
                            <input name="adsense_slot" value="<?php echo $this->escape((string) ($placement['adsense_slot'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 font-mono text-sm" placeholder="1234567890">
                        </label>
                    </div>
                </div>
                <div class="grid gap-3">
                    <label class="block">
                        <span class="text-xs font-semibold text-gray-600">Context</span>
                        <select name="display_context" class="mt-1 w-full rounded border-gray-300">
                            <?php foreach (['blog' => 'Any blog page', 'listing' => 'Listing only', 'article' => 'Article only'] as $value => $label) : ?>
                                <option value="<?php echo $value; ?>" <?php echo ($placement['display_context'] ?? 'blog') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Height</span>
                            <input name="reserved_height" type="number" min="90" value="<?php echo (int) ($placement['reserved_height'] ?? 280); ?>" class="mt-1 w-full rounded border-gray-300">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Min words</span>
                            <input name="min_word_count" type="number" min="0" value="<?php echo (int) ($placement['min_word_count'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300">
                        </label>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Format</span>
                            <input name="ad_format" value="<?php echo $this->escape((string) ($placement['ad_format'] ?? 'auto')); ?>" class="mt-1 w-full rounded border-gray-300">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-600">Sort</span>
                            <input name="sort_order" type="number" value="<?php echo (int) ($placement['sort_order'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300">
                        </label>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_enabled" value="1" <?php echo !empty($placement['is_enabled']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                        Enabled
                    </label>
                    <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save placement</button>
                </div>
            </form>
        </section>
    <?php endforeach; ?>
</div>
