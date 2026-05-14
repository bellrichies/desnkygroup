<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Orders</h1>
        <p class="mt-1 text-sm text-gray-600">Track checkout orders and fulfillment status.</p>
    </div>
    <a href="/admin/orders/export?<?php echo http_build_query($filters); ?>" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Export CSV</a>
</div>

<form method="get" class="mb-5 grid gap-3 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200 md:grid-cols-5">
    <input name="q" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Search order or email">
    <select name="status" class="rounded border-gray-300">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $status) : ?>
            <option value="<?php echo $status; ?>" <?php echo ($filters['status'] ?? '') === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
        <?php endforeach; ?>
    </select>
    <input name="from" type="date" value="<?php echo $this->escape((string) ($filters['from'] ?? '')); ?>" class="rounded border-gray-300">
    <input name="to" type="date" value="<?php echo $this->escape((string) ($filters['to'] ?? '')); ?>" class="rounded border-gray-300">
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Filter</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">Order</th>
                <th class="px-4 py-3">Customer</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Payment</th>
                <th class="px-4 py-3">Total</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($orders as $order) : ?>
                <tr>
                    <td class="px-4 py-3 font-semibold text-gray-950"><?php echo $this->escape((string) $order['order_number']); ?></td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) $order['customer_email']); ?></td>
                    <td class="px-4 py-3"><?php echo $this->escape((string) $order['order_status']); ?></td>
                    <td class="px-4 py-3"><?php echo $this->escape((string) $order['payment_status']); ?></td>
                    <td class="px-4 py-3">NGN <?php echo number_format((float) $order['total'], 2); ?></td>
                    <td class="px-4 py-3 text-right"><a href="/admin/orders/<?php echo (int) $order['id']; ?>" class="font-semibold text-blue-700">View</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)) : ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
