<?php

namespace App\Services;

use App\Config;
use App\Repositories\MediaRepository;
use RuntimeException;

/**
 * Handles media metadata and secure image uploads.
 */
class MediaService extends BaseService
{
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

    public function paginated(
        int $page = 1,
        int $perPage = 20,
        ?string $search = null,
        ?string $mediaType = null
    ): array
    {
        return $this->media->paginated($page, $perPage, $search, $mediaType);
    }

    public function typeCounts(?string $search = null): array
    {
        return $this->media->typeCounts($search);
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

        $maxSize = (int) Config::get('security.uploads.max_size', 5242880);
        if ((int) ($file['size'] ?? 0) > $maxSize) {
            throw new RuntimeException('Images must be 5MB or smaller.');
        }

        $originalExtension = strtolower((string) pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $allowedExtensions = (array) Config::get('security.uploads.allowed_extensions', ['jpg', 'jpeg', 'png', 'webp']);
        if (!in_array($originalExtension, $allowedExtensions, true)) {
            throw new RuntimeException('Only JPG, PNG, and WebP images are allowed.');
        }

        $mimeType = (string) mime_content_type((string) $file['tmp_name']);
        $allowedTypes = (array) Config::get('security.uploads.allowed_mime_types', ['image/jpeg', 'image/png', 'image/webp']);
        if (!in_array($mimeType, $allowedTypes, true) || getimagesize((string) $file['tmp_name']) === false) {
            throw new RuntimeException('Only JPG, PNG, and WebP images are allowed.');
        }

        $extension = match ($mimeType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $directory = $this->basePath() . '/public/uploads/media';
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $safeName = strtolower(trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', pathinfo($file['name'], PATHINFO_FILENAME)), '-'));
        $filename = ($safeName !== '' ? $safeName : 'image') . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
        $target = $directory . '/' . $filename;

        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new RuntimeException('The image could not be uploaded.');
        }

        $this->stripImageMetadata($target, $mimeType);
        $dimensions = @getimagesize($target);
        chmod($target, 0644);

        return $this->media->create([
            'path' => '/uploads/media/' . $filename,
            'filename' => $filename,
            'mime_type' => $mimeType,
            'media_type' => $this->categoryForMime($mimeType),
            'size' => (int) $file['size'],
            'width' => is_array($dimensions) ? (int) $dimensions[0] : null,
            'height' => is_array($dimensions) ? (int) $dimensions[1] : null,
            'alt_text' => $data['alt_text'] ?? null,
            'title' => $data['title'] ?? null,
            'uploaded_by' => $userId,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        if ($this->media->find($id) === null) {
            throw new RuntimeException('The media item could not be found.');
        }

        return $this->media->update($id, [
            'title' => $this->limited($data['title'] ?? null, 255, 'Title'),
            'alt_text' => $this->limited($data['alt_text'] ?? null, 255, 'Alt text'),
            'seo_description' => $this->limited($data['seo_description'] ?? null, 320, 'SEO description'),
            'caption' => $this->limited($data['caption'] ?? null, 2000, 'Caption'),
            'tags' => $this->limited($data['tags'] ?? null, 500, 'Tags'),
        ]);
    }

    public function delete(int $id): bool
    {
        $row = $this->media->find($id);
        if ($row !== null) {
            $path = $this->basePath() . '/public' . (string) $row['path'];
            if (is_file($path)) {
                unlink($path);
            }
        }

        return $this->media->delete($id);
    }

    public function replace(int $id, array $file, array $data, ?int $userId = null): int
    {
        $old = $this->media->find($id);
        if ($old === null) {
            throw new RuntimeException('The media item could not be found.');
        }

        $newId = $this->upload($file, $data, $userId);
        $new = $this->media->find($newId);

        try {
            if ($new === null) {
                throw new RuntimeException('The replacement upload could not be read.');
            }
            if ((string) $new['mime_type'] !== (string) $old['mime_type']) {
                throw new RuntimeException('Replace the asset with the same image type to preserve its existing URL.');
            }

            $oldPath = $this->basePath() . '/public' . (string) $old['path'];
            $newPath = $this->basePath() . '/public' . (string) $new['path'];
            if (!is_file($newPath) || !copy($newPath, $oldPath)) {
                throw new RuntimeException('The replacement image could not be stored.');
            }

            $this->media->updateFile($id, [
                'mime_type' => $new['mime_type'],
                'size' => is_file($oldPath) ? filesize($oldPath) : $new['size'],
                'width' => $new['width'] ?? null,
                'height' => $new['height'] ?? null,
                'alt_text' => $data['alt_text'] ?? $old['alt_text'] ?? null,
                'title' => $data['title'] ?? $old['title'] ?? null,
            ]);
            unlink($newPath);
            $this->media->delete($newId);
        } catch (\Throwable $exception) {
            $this->delete($newId);
            throw $exception;
        }

        return $id;
    }

    private function basePath(): string
    {
        return defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
    }

    private function stripImageMetadata(string $path, string $mimeType): void
    {
        if (!function_exists('imagecreatefromjpeg')) {
            return;
        }

        $image = match ($mimeType) {
            'image/png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : false,
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => @imagecreatefromjpeg($path),
        };

        if ($image === false) {
            return;
        }

        match ($mimeType) {
            'image/png' => imagepng($image, $path, 6),
            'image/webp' => function_exists('imagewebp') ? imagewebp($image, $path, 82) : null,
            default => imagejpeg($image, $path, 82),
        };

        imagedestroy($image);
    }

    private function categoryForMime(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'document';
    }

    private function limited(mixed $value, int $maximum, string $label): ?string
    {
        $value = trim((string) ($value ?? ''));
        if (mb_strlen($value) > $maximum) {
            throw new RuntimeException("{$label} must not exceed {$maximum} characters.");
        }

        return $value === '' ? null : $value;
    }
}
