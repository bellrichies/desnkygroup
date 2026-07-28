<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950"><?php echo $this->escape((string) $order['order_number']); ?></h1>
        <p class="mt-1 text-sm text-gray-600"><?php echo $this->escape((string) $order['customer_name']); ?> - <?php echo $this->escape((string) $order['customer_email']); ?></p>
    </div>
    <a href="/admin/orders" class="text-sm font-semibold text-blue-700">Back to orders</a>
</div>

<div class="grid gap-6 lg:grid-cols-[1fr_22rem]">
    <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="font-semibold text-gray-950">Items</h2>
        <div class="mt-4 divide-y divide-gray-100">
            <?php foreach ($order['items'] as $item) : ?>
                <div class="flex justify-between gap-4 py-3 text-sm">
                    <div>
                        <p class="font-semibold text-gray-950"><?php echo $this->escape((string) $item['product_name']); ?></p>
                        <p class="text-gray-500"><?php echo (int) $item['quantity']; ?> x NGN <?php echo number_format((float) $item['unit_price'], 2); ?></p>
                    </div>
                    <p class="font-semibold">NGN <?php echo number_format((float) $item['line_total'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <dl class="mt-5 grid gap-2 border-t border-gray-100 pt-4 text-sm">
            <div class="flex justify-between"><dt>Subtotal</dt><dd>NGN <?php echo number_format((float) $order['subtotal'], 2); ?></dd></div>
            <div class="flex justify-between"><dt>Shipping</dt><dd>NGN <?php echo number_format((float) $order['shipping_cost'], 2); ?></dd></div>
            <div class="flex justify-between font-bold"><dt>Total</dt><dd>NGN <?php echo number_format((float) $order['total'], 2); ?></dd></div>
        </dl>
    </section>

    <aside class="space-y-6">
        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Fulfillment</h2>
            <form method="post" action="/admin/orders/<?php echo (int) $order['id']; ?>/status" class="mt-4 space-y-3">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                <select name="status" class="w-full rounded border-gray-300">
                    <?php foreach ($statuses as $status) : ?>
                        <option value="<?php echo $status; ?>" <?php echo $order['order_status'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="w-full rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Update status</button>
            </form>
        </section>

        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Delivery</h2>
            <p class="mt-3 text-sm text-gray-600"><?php echo nl2br($this->escape((string) $order['shipping_address'])); ?></p>
            <p class="mt-2 text-sm text-gray-600"><?php echo $this->escape((string) $order['shipping_city']); ?>, <?php echo $this->escape((string) $order['shipping_state']); ?></p>
            <p class="mt-2 text-sm text-gray-600"><?php echo $this->escape((string) $order['customer_phone']); ?></p>
        </section>

        <section class="rounded bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <h2 class="font-semibold text-gray-950">Manual refund</h2>
            <form method="post" action="/admin/orders/<?php echo (int) $order['id']; ?>/refund" class="mt-4 space-y-3">
                <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
                <textarea name="refund_notes" required class="w-full rounded border-gray-300" placeholder="Refund reference and notes"></textarea>
                <button class="w-full rounded bg-red-700 px-4 py-2 text-sm font-semibold text-white">Record refund</button>
            </form>
        </section>
    </aside>
</div>
