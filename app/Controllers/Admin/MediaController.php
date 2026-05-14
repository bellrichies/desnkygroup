<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\ActivityLogService;
use App\Services\MediaService;

/**
 * Admin controller for the media library.
 */
class MediaController extends BaseController
{
    public function __construct(
        private MediaService $mediaService,
        private ActivityLogService $activityLogService
    ) {
    }

    public function index(): string
    {
        return $this->view('admin/pages/media/index', [
            'title' => 'Media Library',
            'user' => $this->user(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['label' => 'Media Library', 'url' => null],
            ],
            'media' => $this->mediaService->all($_GET['q'] ?? null),
        ]);
    }

    public function store(): void
    {
        try {
            $id = $this->mediaService->upload(
                $_FILES['image'] ?? [],
                $_POST,
                isset($this->user()['id']) ? (int) $this->user()['id'] : null
            );
            $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_uploaded', 'media', 'Uploaded media #' . $id);
            $this->flash('success', 'Image uploaded successfully.');
        } catch (\Throwable $exception) {
            $this->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/media');
    }

    public function update(string $id): void
    {
        $this->mediaService->update((int) $id, $_POST);
        $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_updated', 'media', 'Updated media #' . $id);
        $this->flash('success', 'Image details updated.');
        $this->redirect('/admin/media');
    }

    public function destroy(string $id): void
    {
        $this->mediaService->delete((int) $id);
        $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_deleted', 'media', 'Deleted media #' . $id);
        $this->flash('success', 'Image deleted.');
        $this->redirect('/admin/media');
    }
}
