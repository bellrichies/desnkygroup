<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->escape($title ?? 'Admin Panel'); ?> - Desnky</title>
    <link rel="stylesheet" href="/assets/css/main.min.css?v=<?php echo (string) @filemtime(dirname(__DIR__, 4) . '/public/assets/css/main.min.css'); ?>">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?php echo (string) @filemtime(dirname(__DIR__, 4) . '/public/assets/css/admin.css'); ?>">
</head>
<body class="admin-body bg-gray-100 text-gray-900">
    <?php if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/login')) : ?>
        <?php echo $content ?? ''; ?>
    <?php else : ?>
        <div class="min-h-screen lg:flex" x-data="{ sidebarOpen: false }">
            <?php echo $this->partial('admin/partials/sidebar', ['user' => $user ?? null]); ?>

            <div class="flex min-h-screen flex-1 flex-col lg:pl-72">
                <?php echo $this->partial('admin/partials/header', [
                    'title' => $title ?? 'Dashboard',
                    'user' => $user ?? null,
                    'csrf_token' => $_SESSION['csrf_token'] ?? '',
                ]); ?>

                <main class="admin-main flex-1 bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
                    <?php echo $this->partial('admin/partials/breadcrumbs', [
                        'breadcrumbs' => $breadcrumbs ?? [],
                    ]); ?>

                    <?php foreach ($_SESSION['flash'] ?? [] as $flash) : ?>
                        <?php
                        $flashType = (string) ($flash['type'] ?? 'info');
                        $flashClass = match ($flashType) {
                            'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                            'error' => 'border-red-200 bg-red-50 text-red-800',
                            'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
                            default => 'border-blue-200 bg-blue-50 text-blue-800',
                        };
                        ?>
                        <div role="<?php echo $flashType === 'error' ? 'alert' : 'status'; ?>" class="mb-4 rounded-xl border px-4 py-3 text-sm font-semibold <?php echo $flashClass; ?>">
                            <?php echo $this->escape((string) ($flash['message'] ?? '')); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['flash']); ?>

                    <?php echo $content ?? ''; ?>
                </main>

                <footer class="border-t border-gray-200 bg-white px-6 py-4 text-sm text-gray-500">
                    &copy; <?php echo date('Y'); ?> Desnky Global Resources Ltd.
                </footer>
            </div>
        </div>

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php endif; ?>
</body>
</html>
