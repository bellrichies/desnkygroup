<?php

namespace App\Services;

use App\Repositories\MediaRepository;
use RuntimeException;

/**
 * Handles media metadata and secure image uploads.
 */
class MediaService extends BaseService
{
    private const MAX_SIZE = 5242880;

    /** @var array<int, string> */
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    public function __construct(private MediaRepository $media)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(?string $search = null): array
    {
        return $this->media->all($search);
    }

    public function getById(int $id): ?array
    {
        return $this->media->find($id);
    }

    public function upload(array $file, array $data, ?int $userId = null): int
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Please choose a valid image file.');
        }

        if ((int) ($file['size'] ?? 0) > self::MAX_SIZE) {
            throw new RuntimeException('Images must be 5MB or smaller.');
        }

        $mimeType = (string) mime_content_type((string) $file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes, true)) {
            throw new RuntimeException('Only JPG, PNG, and WebP images are allowed.');
        }

        $extension = match ($mimeType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $directory = BASE_PATH . '/public/uploads/media';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $safeName = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', pathinfo($file['name'], PATHINFO_FILENAME)), '-'));
        $filename = ($safeName !== '' ? $safeName : 'image') . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
        $target = $directory . '/' . $filename;

        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new RuntimeException('The image could not be uploaded.');
        }

        return $this->media->create([
            'path' => '/uploads/media/' . $filename,
            'filename' => $filename,
            'mime_type' => $mimeType,
            'size' => (int) $file['size'],
            'alt_text' => $data['alt_text'] ?? null,
            'title' => $data['title'] ?? null,
            'uploaded_by' => $userId,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        return $this->media->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $row = $this->media->find($id);
        if ($row !== null) {
            $path = BASE_PATH . '/public' . (string) $row['path'];
            if (is_file($path)) {
                unlink($path);
            }
        }

        return $this->media->delete($id);
    }
}
