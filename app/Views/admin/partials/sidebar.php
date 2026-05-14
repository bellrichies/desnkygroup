<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard', PHP_URL_PATH);
$groups = [
    'Main' => [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'grid'],
    ],
    'Content' => [
        ['label' => 'Pages', 'url' => '/admin/pages', 'icon' => 'file', 'permission' => 'pages.view'],
        ['label' => 'Services', 'url' => '/admin/services', 'icon' => 'briefcase', 'permission' => 'services.view'],
        ['label' => 'Projects', 'url' => '/admin/projects', 'icon' => 'image', 'permission' => 'projects.view'],
        ['label' => 'Media Library', 'url' => '/admin/media', 'icon' => 'image', 'permission' => 'media.view'],
    ],
    'Ecommerce' => [
        ['label' => 'Products', 'url' => '/admin/products', 'icon' => 'box', 'permission' => 'products.view'],
        ['label' => 'Categories', 'url' => '/admin/product-categories', 'icon' => 'tag', 'permission' => 'products.view'],
        ['label' => 'Orders', 'url' => '/admin/orders', 'icon' => 'receipt', 'permission' => 'orders.view'],
        ['label' => 'Customers', 'url' => '/admin/customers', 'icon' => 'users', 'permission' => 'orders.view'],
    ],
    'Admin' => [
        ['label' => 'Users', 'url' => '/admin/users', 'icon' => 'user', 'permission' => 'admins.view'],
        ['label' => 'Roles', 'url' => '/admin/roles', 'icon' => 'shield', 'permission' => 'roles.view'],
        ['label' => 'Permissions', 'url' => '/admin/permissions', 'icon' => 'key', 'permission' => 'permissions.view'],
        ['label' => 'Activity Logs', 'url' => '/admin/activity-logs', 'icon' => 'clock', 'permission' => 'activity_logs.view'],
    ],
    'Settings' => [
        ['label' => 'Site Settings', 'url' => '/admin/settings', 'icon' => 'settings'],
    ],
];
?>

<aside
    class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full bg-slate-950 text-white transition lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
    aria-label="Admin navigation"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-white/10 px-6 py-5">
            <a href="/admin/dashboard" class="block text-xl font-bold">Desnky Admin</a>
            <p class="mt-1 text-sm text-slate-300">
                <?php echo $this->escape((string) (($user['role'] ?? 'admin'))); ?>
            </p>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-4 py-6">
            <?php foreach ($groups as $group => $items) : ?>
                <div>
                    <p class="px-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                        <?php echo $this->escape($group); ?>
                    </p>
                    <div class="mt-2 space-y-1">
                        <?php foreach ($items as $item) : ?>
                            <?php if (!empty($item['permission']) && !$this->can($item['permission'])) { continue; } ?>
                            <?php $active = $currentPath === $item['url']; ?>
                            <a
                                href="<?php echo $this->escape($item['url']); ?>"
                                class="flex items-center gap-3 rounded px-3 py-2 text-sm font-medium transition <?php echo $active ? 'bg-blue-700 text-white' : 'text-slate-200 hover:bg-white/10 hover:text-white'; ?>"
                                <?php echo $active ? 'aria-current="page"' : ''; ?>
                            >
                                <span class="h-2 w-2 rounded-full <?php echo $active ? 'bg-white' : 'bg-slate-500'; ?>"></span>
                                <?php echo $this->escape($item['label']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>
</aside>

<div
    class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
></div>
