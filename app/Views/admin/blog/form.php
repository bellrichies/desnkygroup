<?php
$post = $post ?? [];
$categories = $categories ?? [];
$tags = $tags ?? [];
$authors = $authors ?? [];
$statuses = $statuses ?? ['draft', 'scheduled', 'published', 'archived'];
$csrf = (string) ($csrf_token ?? '');
$isEdit = !empty($post['id']);
$dateInput = static fn ($value): string => $value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '';
$categoryIds = array_map('intval', $post['category_ids'] ?? []);
$tagIds = array_map('intval', $post['tag_ids'] ?? []);
$primaryCategoryId = (int) ($post['primary_category_id'] ?? 0);
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape($title); ?></h1>
        <p class="mt-1 text-sm text-gray-600">Editor content is sanitized before storage and rendering.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="/admin/blog" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to posts</a>
        <?php if ($isEdit) : ?>
            <a href="/admin/blog/<?php echo (int) $post['id']; ?>/preview" target="_blank" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Preview</a>
        <?php endif; ?>
    </div>
</div>

<form
    id="blog-post-form"
    method="post"
    action="<?php echo $isEdit ? '/admin/blog/' . (int) $post['id'] : '/admin/blog'; ?>"
    class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]"
>
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">

    <div class="space-y-6">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Post content</h2>
            <div class="mt-4 grid gap-4">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Title <span class="text-red-600">*</span></span>
                    <input id="blog-title" name="title" required value="<?php echo $this->escape((string) ($post['title'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Article title">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Slug</span>
                    <input id="blog-slug" name="slug" value="<?php echo $this->escape((string) ($post['slug'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="auto-generated">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Excerpt</span>
                    <textarea name="excerpt" rows="3" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" maxlength="280" placeholder="Short summary for cards, SEO, and feeds."><?php echo $this->escape((string) ($post['excerpt'] ?? '')); ?></textarea>
                </label>

                <div>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <span class="text-sm font-semibold text-gray-700">Structured HTML content <span class="text-red-600">*</span></span>
                        <div class="flex flex-wrap gap-2" aria-label="Editor shortcuts">
                            <?php foreach (['h2' => 'H2', 'h3' => 'H3', 'p' => 'P', 'ul' => 'UL', 'blockquote' => 'Quote', 'pre' => 'Code'] as $tag => $label) : ?>
                                <button type="button" class="rounded border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50" data-editor-wrap="<?php echo $this->escape($tag); ?>"><?php echo $label; ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <textarea id="blog-content" name="content" rows="22" required class="mt-1 w-full rounded border-gray-300 font-mono text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="<p>Write the article introduction...</p>"><?php echo $this->escape((string) ($post['content'] ?? '')); ?></textarea>
                    <p class="mt-2 text-xs text-gray-500">Allowed content includes headings, paragraphs, lists, links, images, blockquotes, tables, code blocks, and approved embedded media.</p>
                </div>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">SEO metadata</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">SEO title</span>
                    <input name="seo_title" value="<?php echo $this->escape((string) ($post['seo_title'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Leave blank to use post title">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Meta description</span>
                    <textarea name="meta_description" rows="3" maxlength="180" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Recommended length: 140-160 characters."><?php echo $this->escape((string) ($post['meta_description'] ?? '')); ?></textarea>
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Canonical URL</span>
                    <input name="canonical_url" type="url" value="<?php echo $this->escape((string) ($post['canonical_url'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Optional absolute canonical URL">
                </label>
                <label class="block md:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Open Graph image</span>
                    <?php $og = (string) ($post['og_image'] ?? ''); ?>
                    <img id="og_image_preview" src="<?php echo $this->escape($og); ?>" alt="" class="<?php echo $og ? '' : 'hidden'; ?> mb-2 mt-1 w-full rounded object-cover" style="max-height:140px">
                    <input type="hidden" id="og_image" name="og_image" value="<?php echo $this->escape($og); ?>">
                    <button type="button" onclick="openMediaPicker('og_image','og_image_preview')" class="rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Select social image</button>
                </label>
                <input type="hidden" name="robots_index" value="0">
                <label class="flex items-center gap-2 text-sm text-gray-700 md:col-span-2">
                    <input type="checkbox" name="robots_index" value="1" <?php echo array_key_exists('robots_index', $post) ? (!empty($post['robots_index']) ? 'checked' : '') : 'checked'; ?> class="rounded border-gray-300 text-blue-700">
                    Allow public indexing when published
                </label>
            </div>
        </section>
    </div>

    <aside class="space-y-5">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Publishing</h2>
            <div class="mt-4 space-y-3">
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Status</span>
                    <select name="status" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <?php foreach ($statuses as $status) : ?>
                            <option value="<?php echo $this->escape($status); ?>" <?php echo ($post['status'] ?? 'draft') === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Published date</span>
                    <input name="published_at" type="datetime-local" value="<?php echo $this->escape($dateInput($post['published_at'] ?? null)); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-gray-600">Scheduled date</span>
                    <input name="scheduled_at" type="datetime-local" value="<?php echo $this->escape($dateInput($post['scheduled_at'] ?? null)); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_featured" value="1" <?php echo !empty($post['is_featured']) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                    Featured post
                </label>
            </div>
            <button class="mt-5 w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save post</button>
            <?php if ($isEdit) : ?>
                <button type="submit" form="blog-delete-form" class="mt-3 w-full rounded border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Delete post</button>
            <?php endif; ?>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Author</h2>
            <select name="author_id" class="mt-4 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                <option value="">Unassigned</option>
                <?php foreach ($authors as $author) : ?>
                    <option value="<?php echo (int) $author['id']; ?>" <?php echo (int) ($post['author_id'] ?? 0) === (int) $author['id'] ? 'selected' : ''; ?>>
                        <?php echo $this->escape((string) $author['display_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="/admin/blog/authors" class="mt-3 inline-block text-sm font-semibold text-blue-700">Manage authors</a>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Featured image</h2>
            <?php $image = (string) ($post['featured_image'] ?? ''); ?>
            <img id="featured_image_preview" src="<?php echo $this->escape($image); ?>" alt="" class="<?php echo $image ? '' : 'hidden'; ?> mt-4 w-full rounded object-cover" style="max-height:180px">
            <input type="hidden" id="featured_image" name="featured_image" value="<?php echo $this->escape($image); ?>">
            <button type="button" onclick="openMediaPicker('featured_image','featured_image_preview')" class="mt-3 w-full rounded border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Select image</button>
            <label class="mt-3 block">
                <span class="text-xs font-semibold text-gray-600">Image alt text</span>
                <input name="featured_image_alt" value="<?php echo $this->escape((string) ($post['featured_image_alt'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
            </label>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Categories</h2>
            <div class="mt-4 space-y-3">
                <?php foreach ($categories as $category) : ?>
                    <div class="rounded border border-gray-200 p-3">
                        <label class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <input type="checkbox" name="category_ids[]" value="<?php echo (int) $category['id']; ?>" <?php echo in_array((int) $category['id'], $categoryIds, true) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                            <?php echo $this->escape((string) $category['name']); ?>
                        </label>
                        <label class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                            <input type="radio" name="primary_category_id" value="<?php echo (int) $category['id']; ?>" <?php echo $primaryCategoryId === (int) $category['id'] ? 'checked' : ''; ?> class="border-gray-300 text-blue-700">
                            Primary category
                        </label>
                    </div>
                <?php endforeach; ?>
                <?php if ($categories === []) : ?>
                    <p class="text-sm text-gray-500">No categories yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Tags</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                <?php foreach ($tags as $tag) : ?>
                    <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-3 py-1.5 text-sm text-gray-700">
                        <input type="checkbox" name="tag_ids[]" value="<?php echo (int) $tag['id']; ?>" <?php echo in_array((int) $tag['id'], $tagIds, true) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700">
                        <?php echo $this->escape((string) $tag['name']); ?>
                    </label>
                <?php endforeach; ?>
                <?php if ($tags === []) : ?>
                    <p class="text-sm text-gray-500">No tags yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </aside>
</form>

<?php if ($isEdit) : ?>
    <form id="blog-delete-form" method="post" action="/admin/blog/<?php echo (int) $post['id']; ?>/delete" onsubmit="return confirm('Move this blog post to deleted items?')">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf); ?>">
    </form>
<?php endif; ?>

<?php echo $this->partial('admin/partials/media-picker'); ?>

<script>
(function () {
    const titleInput = document.getElementById('blog-title');
    const slugInput = document.getElementById('blog-slug');
    const contentInput = document.getElementById('blog-content');
    let slugEdited = slugInput.value !== '';

    function toSlug(value) {
        return value.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }

    titleInput.addEventListener('input', function () {
        if (!slugEdited) {
            slugInput.value = toSlug(titleInput.value);
        }
    });
    slugInput.addEventListener('input', function () {
        slugEdited = slugInput.value !== '';
    });

    document.querySelectorAll('[data-editor-wrap]').forEach(function (button) {
        button.addEventListener('click', function () {
            const tag = button.dataset.editorWrap;
            const start = contentInput.selectionStart;
            const end = contentInput.selectionEnd;
            const selected = contentInput.value.substring(start, end) || (tag === 'ul' ? '<li>List item</li>' : 'Content');
            const wrapper = tag === 'pre' ? '<pre><code>' + selected + '</code></pre>' : '<' + tag + '>' + selected + '</' + tag + '>';
            contentInput.setRangeText(wrapper, start, end, 'end');
            contentInput.focus();
        });
    });
})();
</script>
