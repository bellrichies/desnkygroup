<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MediaRepository;
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
    private MediaRepository $media;

    public function __construct()
    {
        $connection = DatabaseFactory::make();
        $this->products = new ProductService(new ProductRepository($connection));
        $this->categories = new ProductCategoryService(new ProductCategoryRepository($connection));
        $this->media = new MediaRepository($connection);
    }

    public function index(): string
    {
        $allProducts = $this->products->all($_GET);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $total = count($allProducts);
        return $this->view('admin/products/index', [
            'title' => 'Products',
            'user' => $this->user(),
            'products' => array_slice($allProducts, ($page - 1) * $perPage, $perPage),
            'pagination' => ['page' => $page, 'total_pages' => max(1, (int) ceil($total / $perPage)), 'total' => $total],
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
        $file = $_FILES['csv_file'] ?? null;
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Choose a valid CSV file to import.');
            $this->redirect('/admin/products');
        }
        if (($file['size'] ?? 0) > 2 * 1024 * 1024 || strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION)) !== 'csv') {
            $this->flash('error', 'CSV files must be no larger than 2 MB.');
            $this->redirect('/admin/products');
        }
        $handle = fopen((string) $file['tmp_name'], 'rb');
        if ($handle === false) {
            $this->flash('error', 'The CSV file could not be read.');
            $this->redirect('/admin/products');
        }
        $created = 0;
        $index = 0;
        while (($columns = fgetcsv($handle)) !== false) {
            if ($index++ === 0 && strtolower((string) ($columns[0] ?? '')) === 'name') {
                continue;
            }
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
        fclose($handle);

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
            'gallery' => $product ? $this->products->images((int) $product['id']) : [],
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Products', 'url' => '/admin/products'], ['label' => $title]],
        ]);
    }

    private function persist(?int $id = null): void
    {
        try {
            $existingProduct = $id === null ? null : $this->products->find($id);
            $existingPaths = $id === null ? [] : array_column($this->products->images($id), 'path');
            if (!empty($existingProduct['featured_image'])) {
                $existingPaths[] = (string) $existingProduct['featured_image'];
            }
            $paths = is_array($_POST['gallery_paths'] ?? null) ? $_POST['gallery_paths'] : [];
            $paths = array_values(array_unique(array_filter(array_map(
                fn (mixed $path): string => $this->validatedMediaImage(
                    (string) $path,
                    in_array((string) $path, $existingPaths, true) ? (string) $path : null
                ),
                $paths
            ))));
            if (count($paths) > 5) {
                throw new \InvalidArgumentException('A product can have a maximum of five images.');
            }
            $featured = $this->validatedMediaImage(
                (string) ($_POST['featured_image'] ?? ''),
                in_array((string) ($_POST['featured_image'] ?? ''), $existingPaths, true)
                    ? (string) ($_POST['featured_image'] ?? '')
                    : null
            );
            if ($featured !== '' && !in_array($featured, $paths, true)) {
                throw new \InvalidArgumentException('The featured image must be one of the selected product images.');
            }
            if ($featured === '' && $paths !== []) {
                $featured = $paths[0];
            }
            $_POST['featured_image'] = $featured;
            $_POST['gallery_paths'] = $paths;
            $_POST['created_by'] = $this->user()['id'] ?? 1;
            $id === null ? $this->products->create($_POST) : $this->products->update($id, $_POST);
            $this->flash('success', 'Product saved.');
            $this->redirect('/admin/products');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/products/create' : '/admin/products/' . $id . '/edit');
        }
    }

    private function validatedMediaImage(string $path, ?string $existingPath = null): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        // Preserve pre-Media-Library remote images on existing records, while
        // requiring all new local selections to resolve to an image record.
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            if ($existingPath === $path) {
                return $path;
            }
            throw new \InvalidArgumentException('Select the product image from the Media Library.');
        }

        if (!str_starts_with($path, '/uploads/media/') || str_contains($path, '..')) {
            throw new \InvalidArgumentException('The selected product image path is invalid.');
        }

        $media = $this->media->findImageByPath($path);
        if ($media === null) {
            throw new \InvalidArgumentException('The selected product image is no longer available in the Media Library.');
        }

        $file = dirname(__DIR__, 3) . '/public' . $path;
        if (!is_file($file)) {
            throw new \InvalidArgumentException('The selected product image file is missing. Re-upload or replace it in the Media Library.');
        }

        return $path;
    }
}
