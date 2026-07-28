<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ProductCategoryRepository;
use App\Services\ProductCategoryService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin product category management.
 */
class ProductCategoryController extends BaseController
{
    private ProductCategoryService $categories;

    public function __construct()
    {
        $this->categories = new ProductCategoryService(
            new ProductCategoryRepository(DatabaseFactory::make())
        );
    }

    public function index(): string
    {
        $all = $this->categories->all();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = count($all);
        return $this->view('admin/product-categories/index', [
            'title' => 'Product Categories',
            'user' => $this->user(),
            'categories' => array_slice($all, ($page - 1) * 10, 10),
            'pagination' => ['page' => $page, 'total_pages' => max(1, (int) ceil($total / 10)), 'total' => $total],
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Product Categories']],
            'filters' => $_GET,
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Category');
    }

    public function store(): void
    {
        $this->persist();
    }

    public function edit(string $id): string
    {
        $category = $this->categories->find((int) $id);
        if ($category === null) {
            $this->abort(404, 'Category not found.');
        }

        return $this->form('Edit Category', $category);
    }

    public function update(string $id): void
    {
        $this->persist((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->categories->delete((int) $id);
        $this->flash('success', 'Category removed.');
        $this->redirect('/admin/product-categories');
    }

    private function form(string $title, ?array $category = null): string
    {
        return $this->view('admin/product-categories/form', [
            'title' => $title,
            'user' => $this->user(),
            'category' => $category,
            'categories' => $this->categories->all(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Categories', 'url' => '/admin/product-categories'], ['label' => $title]],
        ]);
    }

    private function persist(?int $id = null): void
    {
        try {
            $id === null ? $this->categories->create($_POST) : $this->categories->update($id, $_POST);
            $this->flash('success', 'Category saved.');
            $this->redirect('/admin/product-categories');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/product-categories/create' : '/admin/product-categories/' . $id . '/edit');
        }
    }
}
