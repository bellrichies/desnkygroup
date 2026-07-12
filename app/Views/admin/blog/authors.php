<?php
$authors = $authors ?? [];
$csrf = (string) ($csrf_token ?? '');
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Blog Authors</h1>
        <p class="mt-1 text-sm text-gray-600">Manage author profiles, bios, and avatars displayed on article pages.</p>
    </div>
    <a href="/admin/blog" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to posts</a>
</div>

<div class="grid gap-6 xl:grid-cols-[24rem_minmax(0,1fr)]">
    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-lg font-bold text-gray-950">Add author</h2>
        <form method="post" action="/admin/blog/authors" class="mt-4 grid gap-3">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
            <input type="hidden" name="is_active" value="0">
            <input name="display_name" required class="rounded border-gray-300" placeholder="Display name">
            <input name="slug" class="rounded border-gray-300 font-mono text-sm" placeholder="optional-slug">
            <input name="title" class="rounded border-gray-300" placeholder="Role or title">
            <input name="email" type="email" class="rounded border-gray-300" placeholder="email@example.com">
            <textarea name="bio" rows="4" class="rounded border-gray-300" placeholder="Short author biography"></textarea>
            <input type="hidden" id="new_author_avatar" name="avatar" value="">
            <button type="button" onclick="openMediaPicker('new_author_avatar','new_author_avatar_preview')" class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Select avatar</button>
            <img id="new_author_avatar_preview" src="" alt="" class="hidden h-24 w-24 rounded-full object-cover">
            <input name="linkedin_url" type="url" class="rounded border-gray-300" placeholder="LinkedIn URL">
            <input name="x_url" type="url" class="rounded border-gray-300" placeholder="X URL">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-700">
                Active
            </label>
            <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Add author</button>
        </form>
    </section>

    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-lg font-bold text-gray-950">Authors</h2>
        <div class="mt-5 grid gap-4">
            <?php foreach ($authors as $author) : ?>
                <?php $avatarId = 'author_avatar_' . (int) $author['id']; ?>
                <?php $previewId = 'author_avatar_preview_' . (int) $author['id']; ?>
                <div class="rounded border border-gray-200 p-4">
                    <form method="post" action="/admin/blog/authors/<?php echo (int) $author['id']; ?>" class="grid gap-4 lg:grid-cols-[8rem_minmax(0,1fr)]">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <input type="hidden" name="is_active" value="0">
                        <div>
                            <?php $avatar = (string) ($author['avatar'] ?? ''); ?>
                            <img id="<?php echo $previewId; ?>" src="<?php echo $this->escape($avatar); ?>" alt="" class="<?php echo $avatar ? '' : 'hidden'; ?> h-24 w-24 rounded-full object-cover">
                            <input type="hidden" id="<?php echo $avatarId; ?>" name="avatar" value="<?php echo $this->escape($avatar); ?>">
                            <button type="button" onclick="openMediaPicker('<?php echo $avatarId; ?>','<?php echo $previewId; ?>')" class="mt-3 rounded border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">Avatar</button>
                        </div>
                        <div class="grid gap-3">
                            <div class="grid gap-3 md:grid-cols-2">
                                <input name="display_name" required value="<?php echo $this->escape((string) $author['display_name']); ?>" class="rounded border-gray-300 font-semibold">
                                <input name="slug" value="<?php echo $this->escape((string) $author['slug']); ?>" class="rounded border-gray-300 font-mono text-sm">
                                <input name="title" value="<?php echo $this->escape((string) ($author['title'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Role or title">
                                <input name="email" type="email" value="<?php echo $this->escape((string) ($author['email'] ?? '')); ?>" class="rounded border-gray-300" placeholder="email@example.com">
                                <input name="linkedin_url" type="url" value="<?php echo $this->escape((string) ($author['linkedin_url'] ?? '')); ?>" class="rounded border-gray-300" placeholder="LinkedIn URL">
                                <input name="x_url" type="url" value="<?php echo $this->escape((string) ($author['x_url'] ?? '')); ?>" class="rounded border-gray-300" placeholder="X URL">
                            </div>
                            <textarea name="bio" rows="3" class="rounded border-gray-300" placeholder="Short author biography"><?php echo $this->escape((string) ($author['bio'] ?? '')); ?></textarea>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="is_active" value="1" <?php echo !empty($author['is_active']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                                    Active
                                </label>
                                <span class="text-xs text-gray-500"><?php echo (int) ($author['post_count'] ?? 0); ?> posts</span>
                                <button class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update author</button>
                            </div>
                        </div>
                    </form>
                    <form method="post" action="/admin/blog/authors/<?php echo (int) $author['id']; ?>/delete" class="mt-3" onsubmit="return confirm('Disable this author? Existing posts remain assigned.')">
                        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
                        <button class="text-sm font-semibold text-red-700 hover:text-red-900">Disable author</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <?php if ($authors === []) : ?>
                <p class="rounded border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">No authors yet.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php echo $this->partial('admin/partials/media-picker'); ?>
