<?php
$sliders    ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Homepage Hero Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage the homepage slider images, copy, CTAs, display order, and visibility.</p>
    </div>
    <a href="/admin/homepage-hero/create" class="inline-flex items-center gap-2 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
        + Add slide
    </a>
</div>

<?php if (empty($sliders)) : ?>
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center">
        <p class="text-sm text-gray-500">No homepage hero slides have been added yet.</p>
        <a href="/admin/homepage-hero/create" class="mt-4 inline-flex rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">
            Create the first slide
        </a>
    </div>
<?php else : ?>
    <form id="hero-reorder-form" method="POST" action="/admin/homepage-hero/reorder">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    </form>

    <div class="mb-3 flex justify-end">
        <button form="hero-reorder-form" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Save order
        </button>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Background</th>
                    <th class="px-5 py-3 text-left">Slide</th>
                    <th class="px-5 py-3 text-left">CTAs</th>
                    <th class="px-5 py-3 text-left">Order</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($sliders as $slider) : ?>
                    <tr class="align-top hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <img
                                src="<?php echo $this->escape((string) $slider['background_image']); ?>"
                                alt="<?php echo $this->escape((string) $slider['heading']); ?>"
                                class="h-20 w-32 rounded object-cover"
                            >
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900"><?php echo $this->escape((string) $slider['heading']); ?></p>
                            <?php if (!empty($slider['caption'])) : ?>
                                <p class="mt-1 line-clamp-2 max-w-md text-gray-500"><?php echo $this->escape((string) $slider['caption']); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-gray-500">
                            <?php if (!empty($slider['primary_cta_label'])) : ?>
                                <p><span class="font-semibold text-gray-700">Primary:</span> <?php echo $this->escape((string) $slider['primary_cta_label']); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($slider['secondary_cta_label'])) : ?>
                                <p class="mt-1"><span class="font-semibold text-gray-700">Secondary:</span> <?php echo $this->escape((string) $slider['secondary_cta_label']); ?></p>
                            <?php endif; ?>
                            <?php if (empty($slider['primary_cta_label']) && empty($slider['secondary_cta_label'])) : ?>
                                <span class="text-gray-400">No CTA buttons</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4">
                            <input
                                form="hero-reorder-form"
                                name="orders[<?php echo (int) $slider['id']; ?>]"
                                type="number"
                                value="<?php echo (int) $slider['sort_order']; ?>"
                                class="w-24 rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            >
                        </td>
                        <td class="px-5 py-4">
                            <form method="POST" action="/admin/homepage-hero/<?php echo (int) $slider['id']; ?>/toggle">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $slider['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'; ?>">
                                    <span class="h-1.5 w-1.5 rounded-full <?php echo $slider['is_active'] ? 'bg-green-500' : 'bg-gray-400'; ?>"></span>
                                    <?php echo $slider['is_active'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </form>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="/admin/homepage-hero/<?php echo (int) $slider['id']; ?>/edit" class="font-semibold text-blue-700 hover:underline">Edit</a>
                            <form method="POST" action="/admin/homepage-hero/<?php echo (int) $slider['id']; ?>/delete" class="inline" onsubmit="return confirm('Delete this hero slide?');">
                                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                <button class="ml-3 font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
