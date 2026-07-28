<?php
/** @var array<string, mixed> $kpis */
$kpis = isset($kpis) && is_array($kpis) ? $kpis : [];
/** @var array<int, array<string, mixed>> $recentInquiries */
$recentInquiries = array_slice(isset($recentInquiries) && is_array($recentInquiries) ? $recentInquiries : [], 0, 5);
/** @var array<int, array<string, mixed>> $recentOrders */
$recentOrders = array_slice(isset($recentOrders) && is_array($recentOrders) ? $recentOrders : [], 0, 5);
/** @var array<int, array<string, mixed>> $recentActivities */
$recentActivities = array_slice(isset($recentActivities) && is_array($recentActivities) ? $recentActivities : [], 0, 5);
$lastUpdated = isset($lastUpdated) ? (string) $lastUpdated : '';
$notice = isset($notice) ? (string) $notice : '';

$cards = [
    ['label' => 'Published pages', 'value' => $kpis['published_pages'] ?? 0, 'url' => '/admin/pages', 'icon' => 'document', 'tone' => 'violet'],
    ['label' => 'Active slides', 'value' => $kpis['active_hero_slides'] ?? 0, 'url' => '/admin/homepage-hero', 'icon' => 'sparkles', 'tone' => 'indigo'],
    ['label' => 'Products', 'value' => $kpis['products'] ?? 0, 'url' => '/admin/products', 'icon' => 'cart', 'tone' => 'sky'],
    ['label' => 'Pending orders', 'value' => $kpis['pending_orders'] ?? 0, 'url' => '/admin/orders', 'icon' => 'clock', 'tone' => 'amber'],
    ['label' => 'Completed revenue', 'value' => '₦' . number_format((float) ($kpis['completed_orders_total'] ?? 0), 0), 'url' => '/admin/orders', 'icon' => 'check-circle', 'tone' => 'emerald'],
    ['label' => 'New inquiries', 'value' => $kpis['unread_inquiries'] ?? 0, 'url' => '/admin/customers?segment=with_inquiries', 'icon' => 'mail', 'tone' => 'rose'],
    ['label' => 'Subscribers', 'value' => $kpis['newsletter_subscribers'] ?? 0, 'url' => '/admin/customers?segment=newsletter', 'icon' => 'users', 'tone' => 'cyan'],
    ['label' => 'Low stock', 'value' => $kpis['low_stock_products'] ?? 0, 'url' => '/admin/products', 'icon' => 'bolt', 'tone' => 'orange'],
    ['label' => 'Admin users', 'value' => $kpis['admin_users'] ?? 0, 'url' => '/admin/users', 'icon' => 'shield-check', 'tone' => 'slate'],
];

$tones = [
    'violet' => ['icon' => 'bg-violet-50 text-violet-700', 'bar' => 'bg-violet-500'],
    'indigo' => ['icon' => 'bg-indigo-50 text-indigo-700', 'bar' => 'bg-indigo-500'],
    'sky' => ['icon' => 'bg-sky-50 text-sky-700', 'bar' => 'bg-sky-500'],
    'amber' => ['icon' => 'bg-amber-50 text-amber-700', 'bar' => 'bg-amber-500'],
    'emerald' => ['icon' => 'bg-emerald-50 text-emerald-700', 'bar' => 'bg-emerald-500'],
    'rose' => ['icon' => 'bg-rose-50 text-rose-700', 'bar' => 'bg-rose-500'],
    'cyan' => ['icon' => 'bg-cyan-50 text-cyan-700', 'bar' => 'bg-cyan-500'],
    'orange' => ['icon' => 'bg-orange-50 text-orange-700', 'bar' => 'bg-orange-500'],
    'slate' => ['icon' => 'bg-slate-100 text-slate-700', 'bar' => 'bg-slate-500'],
];

$statusClasses = static function (string $status): string {
    return match (strtolower($status)) {
        'completed', 'delivered', 'resolved', 'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
        'cancelled', 'failed' => 'bg-rose-50 text-rose-700 ring-rose-600/10',
        'processing', 'in progress', 'read' => 'bg-sky-50 text-sky-700 ring-sky-600/10',
        default => 'bg-amber-50 text-amber-700 ring-amber-600/10',
    };
};
?>

<?php if ($notice !== '') : ?>
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        <span class="h-2 w-2 shrink-0 rounded-full bg-blue-500"></span>
        <?php echo $this->escape($notice); ?>
    </div>
<?php endif; ?>

<div class="space-y-4">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-violet-600">Control centre</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Business overview</h1>
            <p class="mt-1 text-sm text-slate-500">Key performance and recent operations in one view.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="mr-1 text-xs text-slate-400">Updated <?php echo $this->escape($lastUpdated); ?></span>
            <a href="/admin/products/create" class="inline-flex items-center gap-1.5 rounded-lg bg-violet-700 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-violet-800">
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'plus', 'class' => 'h-4 w-4']); ?> Product
            </a>
            <a href="/admin/pages/create" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'plus', 'class' => 'h-4 w-4']); ?> Page
            </a>
            <a href="/admin/dashboard" aria-label="Refresh dashboard" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-900">
                <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-4 w-4']); ?>
            </a>
        </div>
    </header>

    <section aria-label="Key performance indicators" class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-6">
        <?php foreach ($cards as $index => $card) : ?>
            <?php $tone = $tones[$card['tone']] ?? $tones['slate']; ?>
            <a href="<?php echo $this->escape((string) $card['url']); ?>" class="group relative min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md <?php echo $index === 4 ? 'col-span-2 md:col-span-1 xl:col-span-2' : ''; ?>">
                <span class="absolute inset-y-0 left-0 w-1 <?php echo $tone['bar']; ?>"></span>
                <div class="flex items-start justify-between gap-2">
                    <span class="min-w-0">
                        <span class="block truncate text-[11px] font-semibold uppercase tracking-wide text-slate-500"><?php echo $this->escape((string) $card['label']); ?></span>
                        <span class="mt-1.5 block truncate text-xl font-bold tracking-tight text-slate-950"><?php echo $this->escape((string) $card['value']); ?></span>
                    </span>
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg <?php echo $tone['icon']; ?>">
                        <?php echo $this->partial('frontend/partials/icon', ['name' => $card['icon'], 'class' => 'h-4 w-4']); ?>
                    </span>
                </div>
            </a>
        <?php endforeach; ?>
    </section>

    <section class="grid gap-4 xl:grid-cols-3">
        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Recent inquiries</h2>
                    <p class="text-[11px] text-slate-400">Latest customer messages</p>
                </div>
                <a href="/admin/customers?segment=with_inquiries" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-700 hover:text-violet-900">View all <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-3.5 w-3.5']); ?></a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($recentInquiries as $inquiry) : ?>
                    <?php $inquiryStatus = (string) ($inquiry['status'] ?? 'new'); ?>
                    <div class="flex min-h-[3.85rem] items-center gap-3 px-4 py-2.5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-violet-50 text-xs font-bold uppercase text-violet-700"><?php echo $this->escape(substr((string) ($inquiry['full_name'] ?? 'C'), 0, 1)); ?></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-slate-900"><?php echo $this->escape((string) ($inquiry['full_name'] ?? 'Customer')); ?></p>
                            <p class="mt-0.5 truncate text-[11px] text-slate-500"><?php echo $this->escape((string) ($inquiry['subject'] ?? 'General inquiry')); ?></p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize ring-1 ring-inset <?php echo $statusClasses($inquiryStatus); ?>"><?php echo $this->escape($inquiryStatus); ?></span>
                            <p class="mt-1 text-[10px] text-slate-400"><?php echo $this->escape($this->formatDate($inquiry['created_at'] ?? time(), 'M j')); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($recentInquiries === []) : ?><p class="px-4 py-10 text-center text-xs text-slate-400">No inquiries yet.</p><?php endif; ?>
            </div>
        </article>

        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Recent orders</h2>
                    <p class="text-[11px] text-slate-400">Latest sales transactions</p>
                </div>
                <a href="/admin/orders" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-700 hover:text-violet-900">View all <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-3.5 w-3.5']); ?></a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($recentOrders as $order) : ?>
                    <?php $orderStatus = (string) ($order['order_status'] ?? 'pending'); ?>
                    <div class="flex min-h-[3.85rem] items-center gap-3 px-4 py-2.5">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-sky-50 text-sky-700"><?php echo $this->partial('frontend/partials/icon', ['name' => 'cart', 'class' => 'h-4 w-4']); ?></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold text-slate-900"><?php echo $this->escape((string) ($order['order_number'] ?? 'Order')); ?></p>
                            <p class="mt-0.5 truncate text-[11px] text-slate-500"><?php echo $this->escape((string) ($order['customer_name'] ?? 'Customer')); ?></p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-xs font-bold text-slate-900">₦<?php echo $this->escape(number_format((float) ($order['total'] ?? 0), 0)); ?></p>
                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize ring-1 ring-inset <?php echo $statusClasses($orderStatus); ?>"><?php echo $this->escape($orderStatus); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($recentOrders === []) : ?><p class="px-4 py-10 text-center text-xs text-slate-400">No orders yet.</p><?php endif; ?>
            </div>
        </article>

        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-950">Recent activity</h2>
                    <p class="text-[11px] text-slate-400">Latest administrative changes</p>
                </div>
                <a href="/admin/activity-logs" class="inline-flex items-center gap-1 text-xs font-semibold text-violet-700 hover:text-violet-900">View all <?php echo $this->partial('frontend/partials/icon', ['name' => 'arrow-right', 'class' => 'h-3.5 w-3.5']); ?></a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php foreach ($recentActivities as $activity) : ?>
                    <div class="flex min-h-[3.85rem] items-center gap-3 px-4 py-2.5">
                        <span class="relative grid h-8 w-8 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-600">
                            <?php echo $this->partial('frontend/partials/icon', ['name' => 'bolt', 'class' => 'h-4 w-4']); ?>
                            <span class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500"></span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-semibold capitalize text-slate-900"><?php echo $this->escape(str_replace('_', ' ', (string) ($activity['action'] ?? 'Activity'))); ?></p>
                            <p class="mt-0.5 truncate text-[11px] text-slate-500"><?php echo $this->escape((string) ($activity['description'] ?? $activity['module'] ?? '')); ?></p>
                        </div>
                        <time class="shrink-0 text-[10px] text-slate-400"><?php echo $this->escape($this->formatDate($activity['created_at'] ?? time(), 'M j, g:i A')); ?></time>
                    </div>
                <?php endforeach; ?>
                <?php if ($recentActivities === []) : ?><p class="px-4 py-10 text-center text-xs text-slate-400">No activity recorded yet.</p><?php endif; ?>
            </div>
        </article>
    </section>
</div>
