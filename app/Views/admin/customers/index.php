<?php
$segments = [
    '' => 'All customers',
    'with_orders' => 'With orders',
    'with_inquiries' => 'With inquiries',
    'newsletter' => 'Newsletter subscribers',
];
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Customers</h1>
        <p class="mt-1 text-sm text-gray-600">Unified customer view from orders, contact inquiries, and newsletter subscribers.</p>
    </div>
    <a href="/admin/customers/export?<?php echo http_build_query($filters); ?>" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Export CSV</a>
</div>

<section class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <div class="rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm font-medium text-gray-600">Customers</p>
        <p class="mt-2 text-2xl font-bold text-gray-950"><?php echo number_format((int) ($metrics['total'] ?? 0)); ?></p>
    </div>
    <div class="rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm font-medium text-gray-600">With orders</p>
        <p class="mt-2 text-2xl font-bold text-gray-950"><?php echo number_format((int) ($metrics['with_orders'] ?? 0)); ?></p>
    </div>
    <div class="rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm font-medium text-gray-600">With inquiries</p>
        <p class="mt-2 text-2xl font-bold text-gray-950"><?php echo number_format((int) ($metrics['with_inquiries'] ?? 0)); ?></p>
    </div>
    <div class="rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm font-medium text-gray-600">Subscribers</p>
        <p class="mt-2 text-2xl font-bold text-gray-950"><?php echo number_format((int) ($metrics['subscribers'] ?? 0)); ?></p>
    </div>
    <div class="rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
        <p class="text-sm font-medium text-gray-600">Total spent</p>
        <p class="mt-2 text-2xl font-bold text-gray-950">NGN <?php echo number_format((float) ($metrics['total_spent'] ?? 0), 2); ?></p>
    </div>
</section>

<form method="get" class="mb-5 grid gap-3 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200 md:grid-cols-[1fr_14rem_auto]">
    <input name="q" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Search name, email, phone or company">
    <select name="segment" class="rounded border-gray-300">
        <?php foreach ($segments as $value => $label) : ?>
            <option value="<?php echo $this->escape($value); ?>" <?php echo (string) ($filters['segment'] ?? '') === $value ? 'selected' : ''; ?>>
                <?php echo $this->escape($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Filter</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">Customer</th>
                <th class="px-4 py-3">Contact</th>
                <th class="px-4 py-3">Sources</th>
                <th class="px-4 py-3">Orders</th>
                <th class="px-4 py-3">Inquiries</th>
                <th class="px-4 py-3">Newsletter</th>
                <th class="px-4 py-3">Last activity</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($customers as $customer) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-950"><?php echo $this->escape((string) ($customer['name'] ?: 'Unnamed customer')); ?></p>
                        <?php if (!empty($customer['company'])) : ?>
                            <p class="text-xs text-gray-500"><?php echo $this->escape((string) $customer['company']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <a href="mailto:<?php echo $this->escape((string) $customer['email']); ?>" class="font-medium text-blue-700 hover:text-blue-900">
                            <?php echo $this->escape((string) $customer['email']); ?>
                        </a>
                        <?php if (!empty($customer['phone'])) : ?>
                            <p class="mt-1 text-xs"><?php echo $this->escape((string) $customer['phone']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape(implode(', ', $customer['sources'])); ?></td>
                    <td class="px-4 py-3">
                        <p class="font-semibold"><?php echo number_format((int) $customer['order_count']); ?></p>
                        <p class="text-xs text-gray-500">NGN <?php echo number_format((float) $customer['total_spent'], 2); ?></p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold"><?php echo number_format((int) $customer['inquiry_count']); ?></p>
                        <?php if (!empty($customer['last_inquiry_status'])) : ?>
                            <p class="text-xs text-gray-500"><?php echo $this->escape((string) $customer['last_inquiry_status']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php echo $this->escape((string) ($customer['newsletter_status'] ?: 'not subscribed')); ?>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        <?php echo $customer['last_activity_at'] ? $this->escape($this->formatDate($customer['last_activity_at'], 'M j, Y')) : 'N/A'; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="/admin/orders?q=<?php echo urlencode((string) $customer['email']); ?>" class="font-semibold text-blue-700 hover:text-blue-900">Orders</a>
                            <a href="/admin/activity-logs?q=<?php echo urlencode((string) $customer['email']); ?>" class="font-semibold text-gray-700 hover:text-gray-950">Logs</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($customers)) : ?>
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No customers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
