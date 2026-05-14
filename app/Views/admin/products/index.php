<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-950">Products</h1>
        <p class="mt-1 text-sm text-gray-600">Manage ecommerce products, pricing and inventory.</p>
    </div>
    <a href="/admin/products/create" class="rounded bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">New product</a>
</div>

<form method="get" class="mb-5 grid gap-3 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200 md:grid-cols-4">
    <input name="q" value="<?php echo $this->escape((string) ($filters['q'] ?? '')); ?>" class="rounded border-gray-300" placeholder="Search name or SKU">
    <select name="category_id" class="rounded border-gray-300">
        <option value="">All categories</option>
        <?php foreach ($categories as $category) : ?>
            <option value="<?php echo (int) $category['id']; ?>" <?php echo (string) ($filters['category_id'] ?? '') === (string) $category['id'] ? 'selected' : ''; ?>>
                <?php echo $this->escape((string) $category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="status" class="rounded border-gray-300">
        <option value="">All statuses</option>
        <?php foreach (['active', 'inactive', 'discontinued'] as $status) : ?>
            <option value="<?php echo $status; ?>" <?php echo ($filters['status'] ?? '') === $status ? 'selected' : ''; ?>>
                <?php echo ucfirst($status); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="rounded border border-gray-300 bg-white px-4 py-2 text-sm font-semibold hover:bg-gray-50">Filter</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow-sm ring-1 ring-gray-200">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-gray-500">
            <tr>
                <th class="px-4 py-3">Product</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-950"><?php echo $this->escape((string) $product['name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $this->escape((string) $product['sku']); ?></p>
                    </td>
                    <td class="px-4 py-3 text-gray-600"><?php echo $this->escape((string) ($product['category_name'] ?? 'Uncategorized')); ?></td>
                    <td class="px-4 py-3 text-gray-900">NGN <?php echo number_format((float) $product['price'], 2); ?></td>
                    <td class="px-4 py-3">
                        <span class="<?php echo (int) $product['quantity_in_stock'] <= (int) $product['reorder_level'] ? 'text-red-700' : 'text-gray-700'; ?>">
                            <?php echo (int) $product['quantity_in_stock']; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3"><?php echo $this->escape((string) ($product['status'] ?? 'active')); ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="/admin/products/<?php echo (int) $product['id']; ?>/edit" class="font-semibold text-blue-700 hover:text-blue-900">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($products)) : ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No products found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<details class="mt-6 rounded bg-white p-4 shadow-sm ring-1 ring-gray-200">
    <summary class="cursor-pointer font-semibold text-gray-950">Bulk CSV upload</summary>
    <form method="post" action="/admin/products/import" class="mt-4 space-y-3">
        <input type="hidden" name="_token" value="<?php echo $this->escape($csrf_token); ?>">
        <textarea name="csv" rows="5" class="w-full rounded border-gray-300 text-sm" placeholder="name,slug,sku,price,quantity_in_stock,category_id,description"></textarea>
        <button class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Import products</button>
    </form>
</details>
