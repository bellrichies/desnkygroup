<?php
/** @var array<string, mixed> $order */
if (!isset($order) || !is_array($order)) {
    $order = [];
}
$items = is_array($order['items'] ?? null) ? $order['items'] : [];
$payment = ucwords(str_replace('_', ' ', (string) ($order['payment_method'] ?? '')));
?>
<section class="section-band bg-desnky-surface print:bg-white">
    <div class="container-page max-w-5xl">
        <div class="mb-8 rounded-2xl bg-desnky-navy p-7 text-white shadow-card print:bg-white print:p-0 print:text-black print:shadow-none">
            <p class="text-sm font-bold uppercase tracking-[.2em] text-desnky-gold">Order confirmed</p>
            <div class="mt-3 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold sm:text-4xl">Thank you, <?php echo $this->escape((string) $order['customer_name']); ?>.</h1>
                    <p class="mt-2 text-white/75 print:text-gray-600">Your order has been received. Keep the reference below for tracking.</p>
                </div>
                <div class="rounded-xl bg-white/10 px-5 py-4 print:border">
                    <span class="block text-xs uppercase tracking-wide text-white/60 print:text-gray-500">Order reference</span>
                    <strong class="mt-1 block text-xl"><?php echo $this->escape((string) $order['order_number']); ?></strong>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_20rem]">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
                <h2 class="text-xl font-bold text-desnky-dark">Purchased items</h2>
                <div class="mt-5 divide-y divide-gray-100">
                    <?php foreach ($items as $item) : ?>
                        <div class="grid grid-cols-[1fr_auto] gap-4 py-4">
                            <div><p class="font-semibold text-desnky-dark"><?php echo $this->escape((string) $item['product_name']); ?></p><p class="text-sm text-desnky-muted">Quantity <?php echo (int) $item['quantity']; ?> × ₦<?php echo number_format((float) $item['unit_price'], 2); ?></p></div>
                            <strong>₦<?php echo number_format((float) $item['line_total'], 2); ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>
                <dl class="ml-auto mt-4 max-w-sm space-y-3 border-t pt-5">
                    <div class="flex justify-between"><dt>Subtotal</dt><dd>₦<?php echo number_format((float) $order['subtotal'], 2); ?></dd></div>
                    <div class="flex justify-between"><dt>Delivery</dt><dd>₦<?php echo number_format((float) $order['shipping_cost'], 2); ?></dd></div>
                    <div class="flex justify-between border-t pt-3 text-lg font-bold"><dt>Total</dt><dd>₦<?php echo number_format((float) $order['total'], 2); ?></dd></div>
                </dl>
            </div>
            <aside class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="font-bold text-desnky-dark">Customer & delivery</h2>
                    <address class="mt-3 not-italic text-sm leading-6 text-desnky-muted">
                        <?php echo $this->escape((string) $order['customer_name']); ?><br>
                        <?php echo $this->escape((string) $order['customer_email']); ?><br>
                        <?php echo $this->escape((string) $order['customer_phone']); ?><br><br>
                        <?php echo $this->escape((string) $order['shipping_address']); ?><br>
                        <?php echo $this->escape((string) $order['shipping_city']); ?>, <?php echo $this->escape((string) $order['shipping_state']); ?>
                    </address>
                    <p class="mt-4 border-t pt-4 text-sm"><span class="text-desnky-muted">Payment:</span> <strong><?php echo $this->escape($payment); ?></strong></p>
                </div>
                <div class="rounded-2xl border border-desnky-primary/20 bg-desnky-primary-50 p-6">
                    <h2 class="font-bold text-desnky-dark">Track your order</h2>
                    <p class="mt-2 text-sm text-desnky-muted">See the latest fulfilment status at any time.</p>
                    <a class="btn-primary mt-4 w-full" href="/track-order?reference=<?php echo rawurlencode((string) $order['order_number']); ?>">Track order</a>
                </div>
            </aside>
        </div>
        <div class="mt-7 flex flex-wrap gap-3 print:hidden">
            <button type="button" onclick="window.print()" class="btn-secondary">Print receipt</button>
            <a href="/shop/order-confirmation/<?php echo rawurlencode((string) $order['order_number']); ?>/receipt.pdf" class="btn-primary">Download PDF</a>
            <a href="/shop" class="btn-ghost">Continue shopping</a>
        </div>
    </div>
</section>
