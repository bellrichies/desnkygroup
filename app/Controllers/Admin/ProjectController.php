<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use App\Repositories\ProjectRepository;
use App\Services\ActivityLogService;
use App\Services\ProjectService;
use App\Support\DatabaseFactory;

/**
 * Admin CRUD controller for projects and gallery entries.
 */
class ProjectController extends BaseController
{
    private ProjectRepository $projectRepo;

    public function __construct(
        private ProjectService $projectService,
        private ActivityLogService $activityLogService
    ) {
        $this->projectRepo = new ProjectRepository(DatabaseFactory::make());
    }

    public function index(): string
    {
        return $this->view('admin/pages/projects/index', [
            'title' => 'Projects',
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs('Projects'),
            'projects' => $this->projectService->all(),
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Project');
    }

    public function store(): void
    {
        $this->save();
    }

    public function edit(string $id): string
    {
        $project = $this->projectService->getById((int) $id);
        if ($project === null) {
            $this->abort(404, 'Project not found.');
        }

        return $this->form('Edit Project', $project);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->projectService->delete((int) $id);
        $this->log('project_deleted', 'Deleted project #' . $id);
        $this->flash('success', 'Project deleted successfully.');
        $this->redirect('/admin/projects');
    }

    private function save(?int $id = null): void
    {
        try {
            $data = $this->validate($_POST, [
                'title' => 'required|string|max:255',
                'summary' => 'max:500',
                'meta_title' => 'max:255',
            ]);
            $data['is_published'] = isset($_POST['is_published']);

            if ($id === null) {
                $id = $this->projectService->create($data);
                $this->log('project_created', 'Created project #' . $id);
            } else {
                $this->projectService->update($id, $data);
                $this->log('project_updated', 'Updated project #' . $id);
            }

            // Sync gallery images submitted with the form.
            $gallery = \is_array($_POST['gallery'] ?? null) ? $_POST['gallery'] : [];
            $this->projectRepo->syncGallery($id, $gallery);

            $this->flash('success', 'Project saved successfully.');
            $this->redirect('/admin/projects');
        } catch (ValidationException) { // $e unused — flash is set inline
            $this->flash('error', 'Please review the project form.');
            $this->redirect($id === null ? '/admin/projects/create' : '/admin/projects/' . $id . '/edit');
        }
    }

    private function form(string $title, ?array $project = null): string
    {
        $galleryImages = $project ? $this->projectRepo->galleryImages((int) $project['id']) : [];

        return $this->view('admin/pages/projects/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs($title),
            'project' => $project,
            'galleryImages' => $galleryImages,
            'action' => $project ? '/admin/projects/' . $project['id'] : '/admin/projects',
            'csrf_token' => $this->csrf(),
        ]);
    }

    private function breadcrumbs(string $current): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Projects', 'url' => '/admin/projects'],
            ['label' => $current, 'url' => null],
        ];
    }

    private function log(string $action, string $description): void
    {
        $this->activityLogService->record((int) ($this->user()['id'] ?? 0), $action, 'projects', $description);
    }
}
