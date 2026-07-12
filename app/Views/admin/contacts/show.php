<?php
$contact    ??= [];
$csrf_token ??= '';

$statusBadge = static function (string $s): string {
    return match ($s) {
        'new'       => 'bg-blue-100 text-blue-800',
        'read'      => 'bg-gray-100 text-gray-700',
        'responded' => 'bg-green-100 text-green-800',
        'archived'  => 'bg-amber-100 text-amber-700',
        default     => 'bg-gray-100 text-gray-600',
    };
};
?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="/admin/contacts" class="text-sm font-semibold text-blue-700">← Back to inquiries</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900"><?php echo $this->escape((string) ($contact['subject'] ?? '')); ?></h1>
    </div>
    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold <?php echo $statusBadge((string) ($contact['status'] ?? 'new')); ?>">
        <?php echo ucfirst((string) ($contact['status'] ?? 'new')); ?>
    </span>
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_22rem]">

    <!-- Message -->
    <div class="space-y-6">
        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <dt class="font-semibold text-gray-500">Name</dt>
                    <dd class="mt-1 text-gray-900"><?php echo $this->escape((string) ($contact['full_name'] ?? '')); ?></dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500">Email</dt>
                    <dd class="mt-1"><a href="mailto:<?php echo $this->escape((string) ($contact['email'] ?? '')); ?>" class="text-blue-700 hover:underline"><?php echo $this->escape((string) ($contact['email'] ?? '')); ?></a></dd>
                </div>
                <?php if (!empty($contact['phone'])) : ?>
                    <div>
                        <dt class="font-semibold text-gray-500">Phone</dt>
                        <dd class="mt-1 text-gray-900"><?php echo $this->escape((string) $contact['phone']); ?></dd>
                    </div>
                <?php endif; ?>
                <?php if (!empty($contact['company'])) : ?>
                    <div>
                        <dt class="font-semibold text-gray-500">Company</dt>
                        <dd class="mt-1 text-gray-900"><?php echo $this->escape((string) $contact['company']); ?></dd>
                    </div>
                <?php endif; ?>
                <div>
                    <dt class="font-semibold text-gray-500">Source</dt>
                    <dd class="mt-1 text-gray-900"><?php echo $this->escape((string) ($contact['source'] ?? 'contact_form')); ?></dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500">Received</dt>
                    <dd class="mt-1 text-gray-900"><?php echo $this->escape((string) ($contact['created_at'] ?? '')); ?></dd>
                </div>
            </dl>
            <div class="mt-6 border-t border-gray-100 pt-5">
                <h2 class="text-sm font-semibold text-gray-500">Message</h2>
                <div class="mt-3 rounded bg-gray-50 p-4 text-sm leading-7 text-gray-800 whitespace-pre-wrap"><?php echo $this->escape((string) ($contact['message'] ?? '')); ?></div>
            </div>
        </section>

        <!-- Notes form -->
        <section class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Internal notes</h2>
            <form method="POST" action="/admin/contacts/<?php echo (int) ($contact['id'] ?? 0); ?>/notes" class="mt-4 space-y-3">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                <input type="hidden" name="status" value="<?php echo $this->escape((string) ($contact['status'] ?? 'read')); ?>">
                <textarea name="internal_notes" rows="4" class="w-full rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600" placeholder="Add notes visible to admin users only…"><?php echo $this->escape((string) ($contact['internal_notes'] ?? '')); ?></textarea>
                <button class="rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Save notes</button>
            </form>
        </section>
    </div>

    <!-- Sidebar: status actions -->
    <aside class="space-y-5">
        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Update status</h2>
            <form method="POST" action="/admin/contacts/<?php echo (int) ($contact['id'] ?? 0); ?>/status" class="mt-4 space-y-3">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                <select name="status" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                    <?php foreach (['new', 'read', 'responded', 'archived'] as $s) : ?>
                        <option value="<?php echo $s; ?>" <?php echo ($contact['status'] ?? 'new') === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Update status</button>
            </form>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-900">Quick actions</h2>
            <div class="mt-4 space-y-2">
                <a href="mailto:<?php echo $this->escape((string) ($contact['email'] ?? '')); ?>?subject=Re: <?php echo $this->escape((string) ($contact['subject'] ?? '')); ?>" class="block w-full rounded border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Reply by email</a>
                <?php if (!empty($contact['phone'])) : ?>
                    <a href="tel:<?php echo $this->escape((string) $contact['phone']); ?>" class="block w-full rounded border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Call <?php echo $this->escape((string) $contact['phone']); ?></a>
                <?php endif; ?>
            </div>
        </section>

        <form method="POST" action="/admin/contacts/<?php echo (int) ($contact['id'] ?? 0); ?>/delete" onsubmit="return confirm('Permanently delete this inquiry?');">
            <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
            <button class="w-full rounded border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Delete inquiry</button>
        </form>
    </aside>

</div>
