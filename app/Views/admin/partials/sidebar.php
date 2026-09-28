<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard', PHP_URL_PATH);
$groups = [
    'Main' => [
        ['label' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'grid'],
    ],
    'Content' => [
        ['label' => 'Homepage Hero', 'url' => '/admin/homepage-hero', 'permission' => 'settings.view'],
        ['label' => 'Pages',        'url' => '/admin/pages',          'permission' => 'pages.view'],
        ['label' => 'Team Members', 'url' => '/admin/team-members',   'permission' => 'pages.view'],
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
    id="admin-mobile-navigation"
    class="admin-sidebar fixed inset-y-0 left-0 z-40 w-[min(22rem,calc(100vw-2rem))] -translate-x-full bg-slate-950 text-white transition lg:w-72 lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen }"
    aria-label="Admin navigation"
>
    <div class="flex h-full flex-col">
        <div class="border-b border-white/10 px-5 py-5">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <a href="/admin/dashboard" class="block truncate text-xl font-bold" @click="sidebarOpen = false">Desnky Admin</a>
                    <p class="mt-1 truncate text-sm text-slate-300"><?php echo $this->escape((string) ($user['role'] ?? 'admin')); ?></p>
                </div>
                <button
                    type="button"
                    class="admin-sidebar__close inline-flex h-10 w-10 shrink-0 items-center justify-center rounded border border-white/15 text-slate-200 hover:bg-white/10 hover:text-white lg:hidden"
                    @click="sidebarOpen = false"
                    aria-label="Close navigation menu"
                    x-show="sidebarOpen"
                    x-transition.opacity
                >
                    <span aria-hidden="true" class="relative block h-5 w-5">
                        <span class="absolute left-1/2 top-1/2 block h-0.5 w-5 -translate-x-1/2 -translate-y-1/2 rotate-45 bg-current"></span>
                        <span class="absolute left-1/2 top-1/2 block h-0.5 w-5 -translate-x-1/2 -translate-y-1/2 -rotate-45 bg-current"></span>
                    </span>
                </button>
            </div>
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
                                @click="sidebarOpen = false"
                            >
                                <span class="h-2 w-2 rounded-full <?php echo $active ? 'bg-white' : 'bg-slate-500'; ?>"></span>
                                <span class="min-w-0 flex-1 truncate"><?php echo $this->escape($item['label']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>
</aside>

<div
    class="admin-sidebar-backdrop fixed inset-0 z-30 bg-gray-950/60 backdrop-blur-sm lg:hidden"
    x-show="sidebarOpen"
    x-transition.opacity
    @click="sidebarOpen = false"
    aria-hidden="true"
></div>
