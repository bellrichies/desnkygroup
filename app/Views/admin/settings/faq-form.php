<?php
$faq        ??= [];
$csrf_token ??= '';
?>

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-900">Edit FAQ</h1>
    <a href="/admin/settings?tab=faqs" class="text-sm font-semibold text-blue-700">← Back to FAQs</a>
</div>

<form method="POST" action="/admin/settings/faqs/<?php echo (int) ($faq['id'] ?? 0); ?>" class="max-w-3xl space-y-5 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
    <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
    <div class="grid gap-4 sm:grid-cols-2">
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Category</span>
            <input name="category" value="<?php echo $this->escape((string) ($faq['category'] ?? 'General')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
        </label>
        <label class="block">
            <span class="text-sm font-semibold text-gray-700">Sort order</span>
            <input name="sort_order" type="number" value="<?php echo (int) ($faq['sort_order'] ?? 0); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
        </label>
    </div>
    <label class="block">
        <span class="text-sm font-semibold text-gray-700">Question <span class="text-red-600">*</span></span>
        <input name="question" required value="<?php echo $this->escape((string) ($faq['question'] ?? '')); ?>" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-gray-700">Answer <span class="text-red-600">*</span></span>
        <textarea name="answer" required rows="6" class="mt-1 w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"><?php echo $this->escape((string) ($faq['answer'] ?? '')); ?></textarea>
    </label>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" <?php echo ($faq['is_active'] ?? 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-blue-700"> Active
    </label>
    <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
        <a href="/admin/settings?tab=faqs" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
        <button class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Save FAQ</button>
    </div>
</form>
