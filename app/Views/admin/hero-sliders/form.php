<?php
$slider     ??= [];
$csrf_token ??= '';
$action     ??= '/admin/homepage-hero';
$image        = (string) ($slider['background_image'] ?? '');
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?php echo $this->escape($title); ?></h1>
        <p class="mt-1 text-sm text-gray-500">Hero slides appear in display order on the public homepage.</p>
    </div>
    <a href="/admin/homepage-hero" class="text-sm font-semibold text-blue-700">Back to hero settings</a>
</div>

<form method="POST" action="<?php echo $this->escape($action); ?>" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

    <div class="space-y-6">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Slide content</h2>
            <div class="mt-4 grid gap-4">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Heading <span class="text-red-600">*</span></span>
                    <input
                        name="heading"
                        maxlength="255"
                        required
                        value="<?php echo $this->escape((string) ($slider['heading'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="e.g. Integrated Energy, Engineering, Procurement"
                    >
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Caption</span>
                    <textarea
                        name="caption"
                        rows="4"
                        maxlength="1000"
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="Short supporting copy for the slide."
                    ><?php echo $this->escape((string) ($slider['caption'] ?? '')); ?></textarea>
                </label>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">CTA buttons</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Primary CTA label</span>
                    <input
                        name="primary_cta_label"
                        maxlength="120"
                        value="<?php echo $this->escape((string) ($slider['primary_cta_label'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="Request a Quote"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Primary CTA URL</span>
                    <input
                        name="primary_cta_url"
                        maxlength="255"
                        value="<?php echo $this->escape((string) ($slider['primary_cta_url'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="/contact"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Secondary CTA label</span>
                    <input
                        name="secondary_cta_label"
                        maxlength="120"
                        value="<?php echo $this->escape((string) ($slider['secondary_cta_label'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="View Services"
                    >
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Secondary CTA URL</span>
                    <input
                        name="secondary_cta_url"
                        maxlength="255"
                        value="<?php echo $this->escape((string) ($slider['secondary_cta_url'] ?? '')); ?>"
                        class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                        placeholder="/services"
                    >
                </label>
            </div>
        </section>
    </div>

    <aside class="space-y-5">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Publishing</h2>
            <label class="mt-4 flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" <?php echo ($slider['is_active'] ?? 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                Visible on homepage
            </label>
            <label class="mt-4 block">
                <span class="text-sm font-semibold text-gray-700">Display order</span>
                <input
                    name="sort_order"
                    type="number"
                    value="<?php echo (int) ($slider['sort_order'] ?? 0); ?>"
                    class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                >
            </label>
            <div class="mt-5 flex gap-3">
                <button class="flex-1 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save</button>
                <a href="/admin/homepage-hero" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Background image</h2>
            <p class="mt-1 text-xs text-gray-400">Upload or select a wide JPG, PNG, or WebP. Recommended minimum: 1600px wide.</p>

            <div class="mt-4 space-y-3">
                <img
                    id="hero_image_preview"
                    src="<?php echo $this->escape($image); ?>"
                    alt="Hero background preview"
                    class="<?php echo $image ? '' : 'hidden'; ?> h-40 w-full rounded object-cover"
                >
                <input type="hidden" id="hero_background_image" name="background_image" value="<?php echo $this->escape($image); ?>">

                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Upload new image</span>
                    <input
                        name="background_image_upload"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100"
                    >
                </label>

                <button
                    type="button"
                    onclick="openMediaPicker('hero_background_image','hero_image_preview')"
                    class="w-full rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50"
                >
                    <?php echo $image ? 'Select different image' : 'Select from media library'; ?>
                </button>

                <?php if ($image) : ?>
                    <button
                        type="button"
                        onclick="document.getElementById('hero_background_image').value='';document.getElementById('hero_image_preview').classList.add('hidden');"
                        class="w-full rounded border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50"
                    >
                        Remove selected image
                    </button>
                <?php endif; ?>
            </div>
        </section>
    </aside>
</form>

<?php echo $this->partial('admin/partials/media-picker'); ?>
