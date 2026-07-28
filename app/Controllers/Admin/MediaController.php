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
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $category = $this->category($_GET['category'] ?? null);
        $search = $_GET['q'] ?? null;
        $result = $this->mediaService->paginated($page, 20, $search, $category);

        return $this->json([
            'media' => array_map($this->serialize(...), $result['items']),
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total' => $result['total'],
            'has_more' => $result['page'] * $result['per_page'] < $result['total'],
            'category' => $category ?? 'all',
            'counts' => $this->mediaService->typeCounts($search),
        ]);
    }

    public function index(): string
    {
        $search = $_GET['q'] ?? null;
        $result = $this->mediaService->paginated(1, 20, $search);

        return $this->view('admin/pages/media/index', [
            'title' => 'Media Library',
            'user' => $this->user(),
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['label' => 'Media Library', 'url' => null],
            ],
            'media' => $result['items'],
            'total' => $result['total'],
            'hasMore' => $result['per_page'] < $result['total'],
            'search' => (string) ($_GET['q'] ?? ''),
            'categoryCounts' => $this->mediaService->typeCounts($search),
        ]);
    }

    public function detailsJson(string $id): string
    {
        $item = $this->mediaService->getById((int) $id);
        if ($item === null) {
            return $this->json(['success' => false, 'message' => 'Media item not found.'], 404);
        }

        return $this->json(['success' => true, 'media' => $this->serialize($item)]);
    }

    public function store()
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
        $uploadedItems = [];
        $errors = [];
        foreach ($files as $file) {
            try {
                $id = $this->mediaService->upload($file, $_POST, $userId);
                $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_uploaded', 'media', 'Uploaded media #' . $id);
                $uploaded++;
                $item = $this->mediaService->getById($id);
                if ($item !== null) {
                    $uploadedItems[] = $this->serialize($item);
                }
            } catch (\Throwable $exception) {
                $errors[] = ($file['name'] ?? 'file') . ': ' . $exception->getMessage();
            }
        }

        if ($this->wantsJson()) {
            return $this->json([
                'success' => $uploaded > 0,
                'message' => $uploaded > 0
                    ? ($uploaded === 1 ? 'Image uploaded successfully.' : "{$uploaded} images uploaded successfully.")
                    : 'No images were uploaded.',
                'media' => $uploadedItems,
                'errors' => $errors,
            ], $uploaded > 0 ? 201 : 422);
        }

        if ($uploaded > 0) {
            $this->flash('success', $uploaded === 1 ? 'Image uploaded successfully.' : "{$uploaded} images uploaded successfully.");
        }
        foreach ($errors as $error) {
            $this->flash('error', $error);
        }

        $this->redirect('/admin/media');
    }

    public function update(string $id)
    {
        try {
            $this->mediaService->update((int) $id, $_POST);
            $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_updated', 'media', 'Updated media #' . $id);
            if ($this->wantsJson()) {
                $item = $this->mediaService->getById((int) $id);
                return $this->json(['success' => true, 'message' => 'Media details updated.', 'media' => $item ? $this->serialize($item) : null]);
            }
        } catch (\Throwable $exception) {
            if ($this->wantsJson()) {
                return $this->json(['success' => false, 'message' => $exception->getMessage()], 422);
            }
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/media');
        }
        $this->flash('success', 'Media details updated.');
        $this->redirect('/admin/media');
    }

    public function destroy(string $id)
    {
        $this->mediaService->delete((int) $id);
        $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_deleted', 'media', 'Deleted media #' . $id);
        if ($this->wantsJson()) {
            return $this->json(['success' => true, 'message' => 'Image deleted.', 'id' => (int) $id]);
        }
        $this->flash('success', 'Image deleted.');
        $this->redirect('/admin/media');
    }

    public function replace(string $id): string
    {
        try {
            $file = $_FILES['image'] ?? [];
            $mediaId = $this->mediaService->replace(
                (int) $id,
                $file,
                $_POST,
                isset($this->user()['id']) ? (int) $this->user()['id'] : null
            );
            $this->activityLogService->record((int) ($this->user()['id'] ?? 0), 'media_replaced', 'media', "Replaced media #{$id}");
            $item = $this->mediaService->getById($mediaId);

            return $this->json([
                'success' => true,
                'message' => 'Image replaced successfully.',
                'old_id' => (int) $id,
                'media' => $item ? $this->serialize($item) : null,
            ], 201);
        } catch (\Throwable $exception) {
            return $this->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    private function wantsJson(): bool
    {
        return str_contains(strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json')
            || strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    private function serialize(array $item): array
    {
        return [
            'id' => (int) $item['id'],
            'path' => (string) $item['path'],
            'alt' => (string) ($item['alt_text'] ?? $item['title'] ?? $item['filename'] ?? ''),
            'alt_text' => (string) ($item['alt_text'] ?? ''),
            'title' => (string) ($item['title'] ?? ''),
            'name' => (string) ($item['filename'] ?? ''),
            'mime_type' => (string) ($item['mime_type'] ?? ''),
            'media_type' => (string) ($item['media_type'] ?? 'document'),
            'size' => (int) ($item['size'] ?? 0),
            'width' => isset($item['width']) ? (int) $item['width'] : null,
            'height' => isset($item['height']) ? (int) $item['height'] : null,
            'created_at' => (string) ($item['created_at'] ?? ''),
            'seo_description' => (string) ($item['seo_description'] ?? ''),
            'caption' => (string) ($item['caption'] ?? ''),
            'tags' => (string) ($item['tags'] ?? ''),
        ];
    }

    private function category(mixed $category): ?string
    {
        $category = strtolower(trim((string) $category));

        return in_array($category, ['image', 'video', 'document'], true) ? $category : null;
    }
}
