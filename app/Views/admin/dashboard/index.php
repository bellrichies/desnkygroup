<?php if (!empty($notice)) : ?>
    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-6">
        <?php echo $this->escape($notice); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php foreach (['pages', 'products', 'orders', 'contacts'] as $metric) : ?>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-600 text-sm font-medium"><?php echo ucfirst($metric); ?></h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">
                <?php echo $this->escape((string) ($stats[$metric] ?? 0)); ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold mb-4">Quick Actions</h2>
        <div class="space-y-2">
            <a href="/admin/pages" class="block px-4 py-2 bg-blue-50 text-blue-600 rounded">Pages</a>
            <a href="/admin/products" class="block px-4 py-2 bg-green-50 text-green-600 rounded">Products</a>
            <a href="/admin/orders" class="block px-4 py-2 bg-purple-50 text-purple-600 rounded">Orders</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-bold mb-4">System Status</h2>
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Database</span>
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Connected</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">PHP Version</span>
                <span class="text-gray-900"><?php echo $this->escape((string) phpversion()); ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Environment</span>
                <span class="text-gray-900">
                    <?php echo $this->escape((string) \App\Config::get('app.env', 'production')); ?>
                </span>
            </div>
        </div>
    </div>
</div>
