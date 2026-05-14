<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use App\Services\ActivityLogService;
use App\Services\PageService;

/**
 * Admin CRUD controller for CMS pages.
 */
class PageController extends BaseController
{
    public function __construct(
        private PageService $pageService,
        private ActivityLogService $activityLogService
    ) {
    }

    public function index(): string
    {
        return $this->view('admin/pages/pages/index', [
            'title' => 'Pages',
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs('Pages'),
            'pages' => $this->pageService->all(),
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Page');
    }

    public function store(): void
    {
        try {
            $data = $this->validate($_POST, [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'meta_title' => 'max:255',
            ]);
            $data['created_by'] = (int) ($this->user()['id'] ?? 1);
            $data['is_published'] = isset($_POST['is_published']);
            $id = $this->pageService->create($data);
            $this->log('page_created', 'Created page #' . $id);
            $this->flash('success', 'Page created successfully.');
            $this->redirect('/admin/pages');
        } catch (ValidationException $exception) {
            $this->flash('error', 'Please review the page form.');
            $this->redirect('/admin/pages/create');
        }
    }

    public function edit(string $id): string
    {
        $page = $this->pageService->getById((int) $id);
        if ($page === null) {
            $this->abort(404, 'Page not found.');
        }

        return $this->form('Edit Page', $page);
    }

    public function update(string $id): void
    {
        try {
            $data = $this->validate($_POST, [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'meta_title' => 'max:255',
            ]);
            $data['is_published'] = isset($_POST['is_published']);
            $this->pageService->update((int) $id, $data);
            $this->log('page_updated', 'Updated page #' . $id);
            $this->flash('success', 'Page updated successfully.');
            $this->redirect('/admin/pages');
        } catch (ValidationException $exception) {
            $this->flash('error', 'Please review the page form.');
            $this->redirect('/admin/pages/' . $id . '/edit');
        }
    }

    public function destroy(string $id): void
    {
        $this->pageService->delete((int) $id);
        $this->log('page_deleted', 'Deleted page #' . $id);
        $this->flash('success', 'Page deleted successfully.');
        $this->redirect('/admin/pages');
    }

    private function form(string $title, ?array $page = null): string
    {
        return $this->view('admin/pages/pages/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs($title),
            'page' => $page,
            'action' => $page ? '/admin/pages/' . $page['id'] : '/admin/pages',
        ]);
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function breadcrumbs(string $current): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Pages', 'url' => '/admin/pages'],
            ['label' => $current, 'url' => null],
        ];
    }

    private function log(string $action, string $description): void
    {
        $this->activityLogService->record((int) ($this->user()['id'] ?? 0), $action, 'pages', $description);
    }
}
