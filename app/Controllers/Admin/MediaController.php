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

    /**
     * JSON endpoint used by the media-picker modal in admin forms.
     * GET /admin/media/json?q=search
     */
    public function listJson(): string
    {
        $items = $this->mediaService->all($_GET['q'] ?? null);

        $payload = array_map(static fn (array $item): array => [
            'id'   => (int) $item['id'],
            'path' => (string) $item['path'],
            'alt'  => (string) ($item['alt_text'] ?? $item['title'] ?? $item['filename'] ?? ''),
            'name' => (string) ($item['filename'] ?? ''),
        ], $items);

        return $this->json(['media' => $payload]);
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
        $raw = $_FILES['images'] ?? [];
        $userId = isset($this->user()['id']) ? (int) $this->user()['id'] : null;

        // Transpose PHP's multi-file array into a list of individual file arrays.
        $files = [];
        if (isset($raw['name']) && is_array($raw['name'])) {
            foreach (array_keys($raw['name']) as $i) {
                $files[] = [
                    'name'     => $raw['name'][$i],
                    'type'     => $raw['type'][$i],
                    'tmp_name' => $raw['tmp_name'][$i],
                    'error'    => $raw['error'][$i],
                    'size'     => $raw['size'][$i],
                ];
            }
        }

        $uploaded = 0;
        $errors = [];
        foreach ($files as $file) {
            try {
                $id = $this->mediaService->upload($file, $_POST, $userId);
                $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_uploaded', 'media', 'Uploaded media #' . $id);
                $uploaded++;
            } catch (\Throwable $exception) {
                $errors[] = ($file['name'] ?? 'file') . ': ' . $exception->getMessage();
            }
        }

        if ($uploaded > 0) {
            $this->flash('success', $uploaded === 1 ? 'Image uploaded successfully.' : "{$uploaded} images uploaded successfully.");
        }
        foreach ($errors as $error) {
            $this->flash('error', $error);
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
