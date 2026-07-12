<?php
$contacts   ??= [];
$filters    ??= [];
$statuses   ??= ['new', 'read', 'responded', 'archived'];
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

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Contact Inquiries</h1>
        <p class="mt-1 text-sm text-gray-500"><?php echo count($contacts); ?> entr<?php echo count($contacts) === 1 ? 'y' : 'ies'; ?> matching current filters.</p>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="/admin/contacts" class="mb-5 flex flex-wrap gap-3">
    <input name="q" type="search" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" placeholder="Search name, email, subject…" class="rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
    <select name="status" class="rounded border-gray-300 text-sm shadow-sm focus:border-blue-600 focus:ring-blue-600">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $s) : ?>
            <option value="<?php echo $s; ?>" <?php echo ($filters['status'] ?? '') === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
        <?php endforeach; ?>
    </select>
    <button class="rounded bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Filter</button>
    <?php if (!empty($filters['q']) || !empty($filters['status'])) : ?>
        <a href="/admin/contacts" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Clear</a>
    <?php endif; ?>
</form>

<?php if (empty($contacts)) : ?>
    <div class="rounded-lg border border-dashed border-gray-300 bg-white p-12 text-center text-sm text-gray-400">No inquiries match your filter.</div>
<?php else : ?>
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Name / Email</th>
                    <th class="px-5 py-3 text-left">Subject</th>
                    <th class="px-5 py-3 text-left">Source</th>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($contacts as $c) : ?>
                    <tr class="hover:bg-gray-50 <?php echo $c['status'] === 'new' ? 'font-semibold' : ''; ?>">
                        <td class="px-5 py-3">
                            <span class="block text-gray-900"><?php echo $this->escape((string) $c['full_name']); ?></span>
                            <span class="block text-xs text-gray-400"><?php echo $this->escape((string) $c['email']); ?></span>
                        </td>
                        <td class="max-w-xs truncate px-5 py-3 text-gray-700"><?php echo $this->escape((string) $c['subject']); ?></td>
                        <td class="px-5 py-3 text-gray-500"><?php echo $this->escape((string) ($c['source'] ?? 'contact_form')); ?></td>
                        <td class="px-5 py-3 text-gray-400 whitespace-nowrap"><?php echo $this->escape(substr((string) $c['created_at'], 0, 10)); ?></td>
                        <td class="px-5 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold <?php echo $statusBadge((string) ($c['status'] ?? 'new')); ?>">
                                <?php echo ucfirst((string) ($c['status'] ?? 'new')); ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="/admin/contacts/<?php echo (int) $c['id']; ?>" class="font-semibold text-blue-700 hover:underline">View</a>
                            <form method="POST" action="/admin/contacts/<?php echo (int) $c['id']; ?>/delete" class="inline" onsubmit="return confirm('Delete this inquiry?');">
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
