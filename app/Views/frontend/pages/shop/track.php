<?php
/** @var array<string, mixed>|null $order */
$order = isset($order) && is_array($order) ? $order : null;
/** @var string $reference */
$reference = isset($reference) ? (string) $reference : '';
/** @var bool $searched */
$searched = isset($searched) ? (bool) $searched : false;
$steps = ['pending' => 1, 'confirmed' => 2, 'processing' => 3, 'shipped' => 4, 'delivered' => 5, 'cancelled' => 0];
$current = $order ? ($steps[$order['order_status']] ?? 1) : 0;
?>
<section class="section-band bg-desnky-surface">
    <div class="container-page max-w-4xl">
        <div class="mx-auto max-w-2xl text-center">
            <p class="eyebrow">Order updates</p>
            <h1 class="mt-3 section-heading">Track your order</h1>
            <p class="section-lead">Enter the reference from your confirmation or receipt.</p>
            <form method="get" action="/track-order" class="mt-7 flex flex-col gap-3 rounded-2xl bg-white p-3 shadow-card sm:flex-row">
                <label for="reference" class="sr-only">Order reference</label>
                <input id="reference" name="reference" required pattern="DGR-[0-9]{8}-[0-9]{4}" value="<?php echo $this->escape($reference); ?>" placeholder="DGR-YYYYMMDD-0000" class="form-field flex-1 uppercase">
                <button class="btn-primary">Track order</button>
            </form>
        </div>
        <?php if ($order) : ?>
            <div class="mt-10 rounded-2xl border border-gray-200 bg-white p-6 shadow-card sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div><p class="text-sm text-desnky-muted">Reference</p><h2 class="text-xl font-bold"><?php echo $this->escape((string) $order['order_number']); ?></h2></div>
                    <span class="rounded-full bg-desnky-primary-50 px-4 py-2 text-sm font-bold capitalize text-desnky-primary"><?php echo $this->escape((string) $order['order_status']); ?></span>
                </div>
                <?php if ($order['order_status'] === 'cancelled') : ?>
                    <p class="mt-6 rounded-lg bg-red-50 p-4 text-sm text-red-800">This order was cancelled. Please contact us if you need assistance.</p>
                <?php else : ?>
                    <ol class="mt-8 grid gap-3 sm:grid-cols-5">
                        <?php foreach (['Received', 'Confirmed', 'Processing', 'Shipped', 'Delivered'] as $index => $label) : ?>
                            <?php $done = ($index + 1) <= $current; ?>
                            <li class="rounded-xl border p-4 text-center <?php echo $done ? 'border-desnky-primary bg-desnky-primary-50' : 'border-gray-200'; ?>">
                                <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold <?php echo $done ? 'bg-desnky-primary text-white' : 'bg-gray-100 text-gray-500'; ?>"><?php echo $index + 1; ?></span>
                                <span class="mt-2 block text-xs font-bold"><?php echo $label; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            </div>
        <?php elseif ($searched) : ?>
            <div role="alert" class="mx-auto mt-8 max-w-2xl rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">We could not find an order with that reference. Check the number and try again.</div>
        <?php endif; ?>
    </div>
</section>
