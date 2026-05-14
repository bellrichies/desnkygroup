<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Media Library</h1>
        <p class="mt-1 text-sm text-gray-600">Upload reusable JPG, PNG, and WebP images for pages, services, projects, and SEO.</p>
    </div>
</div>

<form method="POST" action="/admin/media" enctype="multipart/form-data" class="mb-6 rounded border border-gray-200 bg-white p-5 shadow-sm">
    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
    <div class="grid gap-4 md:grid-cols-4">
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required class="rounded border border-gray-300 px-3 py-2 text-sm md:col-span-2">
        <input name="title" placeholder="Image title" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
        <input name="alt_text" placeholder="Alt text" class="rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
    </div>
    <button class="mt-4 rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Upload Image</button>
</form>

<div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
    <?php foreach ($media as $item) : ?>
        <article class="overflow-hidden rounded border border-gray-200 bg-white shadow-sm">
            <img src="<?php echo $this->escape((string) $item['path']); ?>" alt="<?php echo $this->escape((string) ($item['alt_text'] ?? $item['title'] ?? $item['filename'])); ?>" class="h-44 w-full object-cover">
            <div class="space-y-3 p-4">
                <p class="break-all text-sm font-semibold text-gray-900"><?php echo $this->escape((string) $item['filename']); ?></p>
                <p class="break-all text-xs text-gray-500"><?php echo $this->escape((string) $item['path']); ?></p>
                <form method="POST" action="/admin/media/<?php echo (int) $item['id']; ?>" class="space-y-2">
                    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
                    <input name="title" value="<?php echo $this->escape((string) ($item['title'] ?? '')); ?>" placeholder="Title" class="w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <input name="alt_text" value="<?php echo $this->escape((string) ($item['alt_text'] ?? '')); ?>" placeholder="Alt text" class="w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <button class="w-full rounded border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Update</button>
                </form>
                <form method="POST" action="/admin/media/<?php echo (int) $item['id']; ?>/delete" onsubmit="return confirm('Delete this image?');">
                    <input type="hidden" name="_token" value="<?php echo $this->escape((string) ($_SESSION['csrf_token'] ?? '')); ?>">
                    <button class="w-full rounded border border-red-200 px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50">Delete</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
    <?php if (empty($media)) : ?>
        <div class="rounded border border-gray-200 bg-white p-8 text-center text-gray-500 sm:col-span-2 xl:col-span-4">No media uploaded yet.</div>
    <?php endif; ?>
</div>
