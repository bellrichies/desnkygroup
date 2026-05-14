<?php if (!empty($notice)) : ?>
    <div class="mb-6 rounded border border-blue-200 bg-blue-50 px-4 py-3 text-blue-800">
        <?php echo $this->escape($notice); ?>
    </div>
<?php endif; ?>

<?php
$cards = [
    ['label' => 'Published Pages', 'value' => $kpis['published_pages'] ?? 0, 'url' => '/admin/pages'],
    ['label' => 'Products Listed', 'value' => $kpis['products'] ?? 0, 'url' => '/admin/products'],
    ['label' => 'Pending Orders', 'value' => $kpis['pending_orders'] ?? 0, 'url' => '/admin/orders'],
    [
        'label' => 'Completed Orders',
        'value' => '₦' . number_format((float) ($kpis['completed_orders_total'] ?? 0), 2),
        'url' => '/admin/orders',
    ],
    ['label' => 'New Inquiries', 'value' => $kpis['unread_inquiries'] ?? 0, 'url' => '/admin/pages'],
    ['label' => 'Subscribers', 'value' => $kpis['newsletter_subscribers'] ?? 0, 'url' => '/admin/settings'],
    ['label' => 'Low Stock', 'value' => $kpis['low_stock_products'] ?? 0, 'url' => '/admin/products'],
    ['label' => 'Admin Users', 'value' => $kpis['admin_users'] ?? 0, 'url' => '/admin/users'],
];
?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-gray-600">
        Last updated <?php echo $this->escape((string) ($lastUpdated ?? '')); ?>
    </p>
    <a
        href="/admin/dashboard"
        class="inline-flex items-center justify-center rounded border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
    >
        Refresh
    </a>
</div>

<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <?php foreach ($cards as $card) : ?>
        <a href="<?php echo $this->escape($card['url']); ?>" class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:shadow">
            <span class="text-sm font-medium text-gray-600"><?php echo $this->escape($card['label']); ?></span>
            <span class="mt-3 block text-3xl font-bold text-slate-950">
                <?php echo $this->escape((string) $card['value']); ?>
            </span>
        </a>
    <?php endforeach; ?>
</section>

<section class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-3">
    <div class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-base font-semibold text-gray-950">Quick Actions</h2>
        <div class="mt-4 grid gap-2">
            <a href="/admin/pages" class="rounded bg-blue-50 px-4 py-3 text-sm font-medium text-blue-800 hover:bg-blue-100">
                Create new page
            </a>
            <a href="/admin/products" class="rounded bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 hover:bg-emerald-100">
                Create new product
            </a>
            <a href="/admin/orders" class="rounded bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 hover:bg-amber-100">
                View pending orders
            </a>
            <a href="/admin/activity-logs" class="rounded bg-slate-100 px-4 py-3 text-sm font-medium text-slate-800 hover:bg-slate-200">
                View new activity
            </a>
        </div>
    </div>

    <div class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200 xl:col-span-2">
        <h2 class="text-base font-semibold text-gray-950">Recent Inquiries</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase text-gray-500">
                        <th class="py-2 pr-4">Name</th>
                        <th class="py-2 pr-4">Subject</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($recentInquiries ?? [] as $inquiry) : ?>
                        <tr>
                            <td class="py-3 pr-4 font-medium text-gray-900">
                                <?php echo $this->escape((string) ($inquiry['full_name'] ?? '')); ?>
                            </td>
                            <td class="py-3 pr-4 text-gray-600">
                                <?php echo $this->escape((string) ($inquiry['subject'] ?? '')); ?>
                            </td>
                            <td class="py-3 pr-4">
                                <span class="rounded bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                    <?php echo $this->escape((string) ($inquiry['status'] ?? 'new')); ?>
                                </span>
                            </td>
                            <td class="py-3 text-gray-600">
                                <?php echo $this->escape($this->formatDate($inquiry['created_at'] ?? time(), 'M j, Y')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentInquiries)) : ?>
                        <tr><td colspan="4" class="py-6 text-center text-gray-500">No inquiries yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-2">
    <div class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-base font-semibold text-gray-950">Recent Orders</h2>
        <div class="mt-4 space-y-3">
            <?php foreach ($recentOrders ?? [] as $order) : ?>
                <div class="flex items-center justify-between rounded border border-gray-200 px-4 py-3">
                    <div>
                        <p class="font-medium text-gray-950">
                            <?php echo $this->escape((string) ($order['order_number'] ?? 'Order')); ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <?php echo $this->escape((string) ($order['customer_name'] ?? 'Customer')); ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-950">
                            ₦<?php echo $this->escape(number_format((float) ($order['total'] ?? 0), 2)); ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo $this->escape((string) ($order['order_status'] ?? 'pending')); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($recentOrders)) : ?>
                <p class="py-6 text-center text-sm text-gray-500">No orders yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="text-base font-semibold text-gray-950">Recent Activity</h2>
        <div class="mt-4 space-y-3">
            <?php foreach ($recentActivities ?? [] as $activity) : ?>
                <div class="rounded border border-gray-200 px-4 py-3">
                    <div class="flex items-center justify-between gap-4">
                        <p class="font-medium text-gray-950">
                            <?php echo $this->escape((string) ($activity['action'] ?? 'activity')); ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo $this->escape($this->formatDate($activity['created_at'] ?? time(), 'M j, g:i A')); ?>
                        </p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                        <?php echo $this->escape((string) ($activity['description'] ?? $activity['module'] ?? '')); ?>
                    </p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($recentActivities)) : ?>
                <p class="py-6 text-center text-sm text-gray-500">No activity recorded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
