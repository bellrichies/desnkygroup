<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Exceptions\ValidationException;
use App\Services\ActivityLogService;
use App\Services\ServiceService;
use App\Repositories\MediaRepository;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin CRUD controller for services.
 */
class ServiceController extends BaseController
{
    public function __construct(
        private ServiceService $serviceService,
        private ActivityLogService $activityLogService
    ) {
    }

    public function index(): string
    {
        return $this->view('admin/pages/services/index', [
            'title' => 'Services',
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs('Services'),
            'services' => $this->serviceService->all(),
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Service');
    }

    public function store(): void
    {
        $this->save();
    }

    public function edit(string $id): string
    {
        $service = $this->serviceService->getById((int) $id);
        if ($service === null) {
            $this->abort(404, 'Service not found.');
        }

        return $this->form('Edit Service', $service);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->serviceService->delete((int) $id);
        $this->log('service_deleted', 'Deleted service #' . $id);
        $this->flash('success', 'Service deleted successfully.');
        $this->redirect('/admin/services');
    }

    private function save(?int $id = null): void
    {
        try {
            $data = $this->validate($_POST, [
                'title' => 'required|string|max:255',
                'summary' => 'max:500',
                'meta_title' => 'max:255',
            ]);
            $data['featured_image'] = $this->validatedMediaImage(
                (string) ($_POST['featured_image'] ?? ''),
                $id === null ? null : (string) (($this->serviceService->getById($id)['featured_image'] ?? ''))
            );
            $data['is_published'] = isset($_POST['is_published']);
            if ($id === null) {
                $id = $this->serviceService->create($data);
                $this->log('service_created', 'Created service #' . $id);
            } else {
                $this->serviceService->update($id, $data);
                $this->log('service_updated', 'Updated service #' . $id);
            }

            $this->flash('success', 'Service saved successfully.');
            $this->redirect('/admin/services');
        } catch (ValidationException $exception) {
            $this->flash('error', 'Please review the service form.');
            $this->redirect($id === null ? '/admin/services/create' : '/admin/services/' . $id . '/edit');
        } catch (Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect($id === null ? '/admin/services/create' : '/admin/services/' . $id . '/edit');
        }
    }

    private function validatedMediaImage(string $path, ?string $existingPath = null): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            if ($existingPath === $path) {
                return $path;
            }
            throw new \InvalidArgumentException('Select the service image from the Media Library.');
        }

        if (!str_starts_with($path, '/uploads/media/') || str_contains($path, '..')) {
            throw new \InvalidArgumentException('The selected service image path is invalid.');
        }

        if ((new MediaRepository(DatabaseFactory::make()))->findImageByPath($path) === null) {
            throw new \InvalidArgumentException('The selected service image is no longer available in the Media Library.');
        }

        $file = dirname(__DIR__, 3) . '/public' . $path;
        if (!is_file($file)) {
            throw new \InvalidArgumentException('The selected service image file is missing. Re-upload or replace it in the Media Library.');
        }

        return $path;
    }

    private function form(string $title, ?array $service = null): string
    {
        return $this->view('admin/pages/services/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs($title),
            'service' => $service,
            'action' => $service ? '/admin/services/' . $service['id'] : '/admin/services',
        ]);
    }

    private function breadcrumbs(string $current): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Services', 'url' => '/admin/services'],
            ['label' => $current, 'url' => null],
        ];
    }

    private function log(string $action, string $description): void
    {
        $this->activityLogService->record((int) ($this->user()['id'] ?? 0), $action, 'services', $description);
    }
}
