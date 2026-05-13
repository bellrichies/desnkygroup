<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->escape($title ?? 'Desnky Global Resources'); ?></title>
    <meta
        name="description"
        content="<?php echo $this->escape($meta_description ?? 'Professional solutions'); ?>"
    >
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-blue-600">Desnky</a>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-blue-600">Home</a>
                    <a href="/services" class="text-gray-700 hover:text-blue-600">Services</a>
                    <a href="/contact" class="text-gray-700 hover:text-blue-600">Contact</a>
                    <a href="/shop" class="text-gray-700 hover:text-blue-600">Shop</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <?php echo $content ?? ''; ?>
    </main>

    <footer class="bg-gray-900 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Desnky</h3>
                    <p class="text-gray-400">Professional solutions for enterprise clients</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/" class="hover:text-white">Home</a></li>
                        <li><a href="/contact" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Services</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/services" class="hover:text-white">All Services</a></li>
                        <li><a href="/shop" class="hover:text-white">Products</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2026 Desnky Global Resources. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
