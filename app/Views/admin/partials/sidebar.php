<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard', PHP_URL_PATH);
$groups = [
    'Main' => [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'grid'],
    ],
    'Content' => [
        ['label' => 'Homepage Hero', 'url' => '/admin/homepage-hero', 'permission' => 'settings.view'],
        ['label' => 'Pages',        'url' => '/admin/pages',          'permission' => 'pages.view'],
        ['label' => 'Services',     'url' => '/admin/services',       'permission' => 'services.view'],
        ['label' => 'Projects',     'url' => '/admin/projects',       'permission' => 'projects.view'],
        ['label' => 'Blog Posts',   'url' => '/admin/blog',           'permission' => 'blog.view'],
        ['label' => 'Blog Taxonomy','url' => '/admin/blog/categories','permission' => 'blog.taxonomy'],
        ['label' => 'Blog Authors', 'url' => '/admin/blog/authors',   'permission' => 'blog.edit'],
        ['label' => 'Media Library','url' => '/admin/media',          'permission' => 'media.view'],
    ],
    'Engagement' => [
        ['label' => 'Trusted Clients','url' => '/admin/trusted-clients','permission' => 'settings.view'],
        ['label' => 'Inquiries',      'url' => '/admin/contacts',       'permission' => 'pages.view'],
        ['label' => 'Subscribers',    'url' => '/admin/subscribers',    'permission' => 'pages.view'],
    ],
    'Ecommerce' => [
        ['label' => 'Products',    'url' => '/admin/products',          'permission' => 'products.view'],
        ['label' => 'Categories',  'url' => '/admin/product-categories','permission' => 'products.view'],
        ['label' => 'Orders',      'url' => '/admin/orders',            'permission' => 'orders.view'],
        ['label' => 'Customers',   'url' => '/admin/customers',         'permission' => 'orders.view'],
    ],
    'Admin' => [
        ['label' => 'Users',        'url' => '/admin/users',         'permission' => 'admins.view'],
        ['label' => 'Roles',        'url' => '/admin/roles',         'permission' => 'roles.view'],
        ['label' => 'Permissions',  'url' => '/admin/permissions',   'permission' => 'permissions.view'],
        ['label' => 'Activity Logs','url' => '/admin/activity-logs', 'permission' => 'activity_logs.view'],
    ],
    'Settings' => [
        ['label' => 'Settings', 'url' => '/admin/settings', 'permission' => 'settings.view'],
        ['label' => 'Blog Ads', 'url' => '/admin/blog/ads', 'permission' => 'blog.ads'],
    ],
];

// Determine if a URL prefix is "active" (handles sub-paths like /admin/contacts/5).
$isActive = static function (string $url) use ($currentPath): bool {
    if ($url === '/admin/blog') {
        return $currentPath === '/admin/blog'
            || $currentPath === '/admin/blog/create'
            || preg_match('#^/admin/blog/[0-9]+#', $currentPath) === 1;
    }

    return $currentPath === $url || str_starts_with($currentPath, $url . '/');
};
?>

<aside
    class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full bg-slate-950 text-white transition lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
    aria-label="Admin navigation"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-white/10 px-6 py-5">
            <a href="/admin/dashboard" class="block text-xl font-bold">Desnky Admin</a>
            <p class="mt-1 text-sm text-slate-300"><?php echo $this->escape((string) ($user['role'] ?? 'admin')); ?></p>
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
                            <?php $active = $isActive($item['url']); ?>
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
