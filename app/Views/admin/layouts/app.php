<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->escape($title ?? 'Admin Panel'); ?> - Desnky</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <aside class="w-64 bg-gray-900 text-white p-6">
            <h1 class="text-2xl font-bold mb-8">Desnky Admin</h1>
            <nav class="space-y-4">
                <a href="/admin/dashboard" class="block px-4 py-2 rounded hover:bg-gray-800">Dashboard</a>
                <a href="/admin/pages" class="block px-4 py-2 rounded hover:bg-gray-800">Pages</a>
                <a href="/admin/products" class="block px-4 py-2 rounded hover:bg-gray-800">Products</a>
                <a href="/admin/orders" class="block px-4 py-2 rounded hover:bg-gray-800">Orders</a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-900">
                        <?php echo $this->escape($title ?? 'Dashboard'); ?>
                    </h2>
                    <span class="text-gray-600">
                        Welcome, <?php echo $this->escape($user['full_name'] ?? 'Admin'); ?>
                    </span>
                </div>
            </header>

            <main class="flex-1 overflow-auto bg-gray-50">
                <div class="p-6">
                    <?php echo $content ?? ''; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
