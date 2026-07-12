<?php
$activeTab  ??= 'site';
$fieldGroups ??= [];
$values     ??= [];
$faqs       ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage site configuration and global content.</p>
    </div>
    <form method="post" action="/admin/settings/cache-clear">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
        <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Clear cache</button>
    </form>
</div>

<!-- Tab bar -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex gap-6 text-sm font-semibold" aria-label="Settings tabs">
        <a href="/admin/settings?tab=site"
           class="<?php echo $activeTab === 'site' ? 'border-b-2 border-blue-700 text-blue-700' : 'text-gray-500 hover:text-gray-800'; ?> pb-3">
            Site Settings
        </a>
        <a href="/admin/settings?tab=faqs"
           class="<?php echo $activeTab === 'faqs' ? 'border-b-2 border-blue-700 text-blue-700' : 'text-gray-500 hover:text-gray-800'; ?> pb-3">
            FAQs
        </a>
    </nav>
</div>

<?php if ($activeTab === 'site') : ?>
<!-- ── Site Settings ──────────────────────────────────────────────────────── -->
<form method="post" action="/admin/settings" class="space-y-6">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">

    <?php foreach ($fieldGroups as $groupName => $fields) : ?>
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="text-lg font-semibold text-gray-950"><?php echo $this->escape((string) $groupName); ?></h2>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <?php foreach ($fields as $field) : ?>
                    <?php
                    $key        = (string) $field['key'];
                    $type       = (string) $field['type'];
                    $value      = (string) ($values[$key] ?? '');
                    $isTextarea = $type === 'textarea';
                    ?>
                    <label class="block <?php echo $isTextarea ? 'lg:col-span-2' : ''; ?>">
                        <span class="text-sm font-semibold text-gray-700">
                            <?php echo $this->escape((string) $field['label']); ?>
                            <?php if (!empty($field['required'])) : ?><span class="text-red-600">*</span><?php endif; ?>
                        </span>
                        <?php if ($isTextarea) : ?>
                            <textarea name="settings[<?php echo $this->escape($key); ?>]" rows="3" <?php echo !empty($field['required']) ? 'required' : ''; ?> class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape($value); ?></textarea>
                        <?php else : ?>
                            <input type="<?php echo in_array($type, ['email', 'url'], true) ? $type : 'text'; ?>" name="settings[<?php echo $this->escape($key); ?>]" value="<?php echo $this->escape($value); ?>" <?php echo !empty($field['required']) ? 'required' : ''; ?> class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                        <?php endif; ?>
                        <?php if (!empty($field['help'])) : ?>
                            <span class="mt-1 block text-xs text-gray-400"><?php echo $this->escape((string) $field['help']); ?></span>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>

    <div class="flex justify-end gap-3">
        <a href="/admin/dashboard" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
        <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save settings</button>
    </div>
</form>

<?php else : ?>
<!-- ── FAQs ───────────────────────────────────────────────────────────────── -->
<div class="space-y-6">

    <!-- Add FAQ form -->
    <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="font-semibold text-gray-950">Add new FAQ</h2>
        <form method="POST" action="/admin/settings/faqs" class="mt-4 space-y-4">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
            <div class="grid gap-4 md:grid-cols-3">
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Category</span>
                    <input name="category" value="General" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. Services">
                </label>
                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Sort order</span>
                    <input name="sort_order" type="number" value="0" class="mt-1 w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
                </label>
                <label class="flex items-end gap-2 pb-1 text-sm text-gray-700">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-700"> Active
                </label>
            </div>
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Question <span class="text-red-600">*</span></span>
                <input name="question" required class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="e.g. What industries does Desnky Global serve?">
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Answer <span class="text-red-600">*</span></span>
                <textarea name="answer" required rows="4" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Full answer text…"></textarea>
            </label>
            <div class="flex justify-end">
                <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Add FAQ</button>
            </div>
        </form>
    </section>

    <!-- FAQ list -->
    <?php if (empty($faqs)) : ?>
        <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-sm text-gray-400">No FAQs yet. Add one above.</div>
    <?php else : ?>
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Question</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Order</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($faqs as $faq) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="max-w-sm px-5 py-3">
                                <p class="font-semibold text-gray-900 line-clamp-2"><?php echo $this->escape((string) $faq['question']); ?></p>
                                <p class="mt-0.5 text-xs text-gray-400 line-clamp-1"><?php echo $this->escape(substr((string) $faq['answer'], 0, 80)); ?>…</p>
                            </td>
                            <td class="px-5 py-3 text-gray-500"><?php echo $this->escape((string) ($faq['category'] ?? 'General')); ?></td>
                            <td class="px-5 py-3 text-gray-400"><?php echo (int) $faq['sort_order']; ?></td>
                            <td class="px-5 py-3">
                                <form method="POST" action="/admin/settings/faqs/<?php echo (int) $faq['id']; ?>/toggle">
                                    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                                    <button class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $faq['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-500'; ?>">
                                        <span class="h-1.5 w-1.5 rounded-full <?php echo $faq['is_active'] ? 'bg-green-500' : 'bg-gray-400'; ?>"></span>
                                        <?php echo $faq['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="/admin/settings/faqs/<?php echo (int) $faq['id']; ?>/edit" class="font-semibold text-blue-700 hover:underline">Edit</a>
                                <form method="POST" action="/admin/settings/faqs/<?php echo (int) $faq['id']; ?>/delete" class="inline" onsubmit="return confirm('Delete this FAQ?');">
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
</div>
<?php endif; ?>
