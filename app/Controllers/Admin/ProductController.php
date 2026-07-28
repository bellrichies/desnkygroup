<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\ProductCategoryService;
use App\Services\ProductService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin product CRUD and inventory management.
 */
class ProductController extends BaseController
{
    private ProductService $products;
    private ProductCategoryService $categories;

    public function __construct()
    {
        $connection = DatabaseFactory::make();
        $this->products = new ProductService(new ProductRepository($connection));
        $this->categories = new ProductCategoryService(new ProductCategoryRepository($connection));
    }

    public function index(): string
    {
        return $this->view('admin/products/index', [
            'title' => 'Products',
            'user' => $this->user(),
            'products' => $this->products->all($_GET),
            'categories' => $this->categories->active(),
            'filters' => $_GET,
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Products']],
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Product');
    }

    public function store(): void
    {
        $this->persist();
    }

    public function edit(string $id): string
    {
        $product = $this->products->find((int) $id);

        if ($product === null) {
            $this->abort(404, 'Product not found.');
        }

        return $this->form('Edit Product', $product);
    }

    public function update(string $id): void
    {
        $this->persist((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->products->delete((int) $id);
        $this->flash('success', 'Product removed.');
        $this->redirect('/admin/products');
    }

    public function stock(string $id): void
    {
        $this->products->updateInventory((int) $id, max(0, (int) ($_POST['quantity_in_stock'] ?? 0)));
        $this->flash('success', 'Inventory updated.');
        $this->redirect('/admin/products/' . (int) $id . '/edit');
    }

    public function import(): void
    {
        $rows = preg_split('/\r\n|\r|\n/', trim((string) ($_POST['csv'] ?? '')));
        $created = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0 && str_contains(strtolower($row), 'name,slug')) {
                continue;
            }

            $columns = str_getcsv($row);
            if (count($columns) < 6) {
                continue;
            }

            try {
                $this->products->create([
                    'name' => $columns[0],
                    'slug' => $columns[1],
                    'sku' => $columns[2],
                    'price' => $columns[3],
                    'quantity_in_stock' => $columns[4],
                    'category_id' => $columns[5],
                    'description' => $columns[6] ?? $columns[0],
                    'is_active' => '1',
                    'status' => 'active',
                    'created_by' => $this->user()['id'] ?? 1,
                ]);
                $created++;
            } catch (Throwable) {
                continue;
            }
        }

        $this->flash('success', "{$created} products imported.");
        $this->redirect('/admin/products');
    }

    private function form(string $title, ?array $product = null): string
    {
        return $this->view('admin/products/form', [
            'title' => $title,
            'user' => $this->user(),
            'product' => $product,
            'categories' => $this->categories->active(),
            'history' => $product ? $this->products->inventoryHistory((int) $product['id']) : [],
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Products', 'url' => '/admin/products'], ['label' => $title]],
        ]);
    }

    private function persist(?int $id = null): void
    {
        try {
            $_POST['created_by'] = $this->user()['id'] ?? 1;
            $id === null ? $this->products->create($_POST) : $this->products->update($id, $_POST);
            $this->flash('success', 'Product saved.');
            $this->redirect('/admin/products');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/products/create' : '/admin/products/' . $id . '/edit');
        }
    }
}
