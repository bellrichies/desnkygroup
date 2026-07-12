<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\HeroSliderRepository;
use App\Repositories\MediaRepository;
use App\Services\ActivityLogService;
use App\Services\HeroSliderService;
use App\Services\MediaService;
use App\Support\DatabaseFactory;

/**
 * Admin CRUD controller for homepage hero slider settings.
 */
class HeroSliderController extends BaseController
{
    private HeroSliderService $sliders;
    private MediaService $media;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->sliders = new HeroSliderService(new HeroSliderRepository($db));
        $this->media = new MediaService(new MediaRepository($db));
        $this->activityLog = new ActivityLogService(new ActivityLogRepository($db));
    }

    public function index(): string
    {
        return $this->view('admin/hero-sliders/index', [
            'title' => 'Homepage Hero Settings',
            'user' => $this->user(),
            'breadcrumbs' => $this->crumbs('Homepage Hero Settings'),
            'sliders' => $this->sliders->all(),
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function create(): string
    {
        return $this->form('Create Hero Slide');
    }

    public function store(): void
    {
        try {
            $data = $this->payloadWithUploadedImage($_POST);
            $data['created_by'] = (int) ($this->user()['id'] ?? 0) ?: null;

            $id = $this->sliders->create($data);
            $this->log('hero_slider_created', "Created homepage hero slide #{$id}");
            $this->flash('success', 'Hero slide created.');
            $this->redirect('/admin/homepage-hero');
        } catch (\Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/homepage-hero/create');
        }
    }

    public function edit(string $id): string
    {
        $slider = $this->sliders->find((int) $id);
        if ($slider === null) {
            $this->abort(404, 'Hero slide not found.');
        }

        return $this->form('Edit Hero Slide', $slider);
    }

    public function update(string $id): void
    {
        try {
            $data = $this->payloadWithUploadedImage($_POST);
            $this->sliders->update((int) $id, $data);
            $this->log('hero_slider_updated', "Updated homepage hero slide #{$id}");
            $this->flash('success', 'Hero slide updated.');
            $this->redirect('/admin/homepage-hero');
        } catch (\Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/admin/homepage-hero/' . $id . '/edit');
        }
    }

    public function destroy(string $id): void
    {
        $this->sliders->delete((int) $id);
        $this->log('hero_slider_deleted', "Deleted homepage hero slide #{$id}");
        $this->flash('success', 'Hero slide deleted.');
        $this->redirect('/admin/homepage-hero');
    }

    public function toggle(string $id): void
    {
        $this->sliders->toggleActive((int) $id);
        $this->log('hero_slider_toggled', "Toggled homepage hero slide #{$id}");
        $this->flash('success', 'Hero slide visibility updated.');
        $this->redirect('/admin/homepage-hero');
    }

    public function reorder(): void
    {
        $this->sliders->reorder((array) ($_POST['orders'] ?? []));
        $this->log('hero_slider_reordered', 'Updated homepage hero slide order');
        $this->flash('success', 'Hero slide order updated.');
        $this->redirect('/admin/homepage-hero');
    }

    private function form(string $title, ?array $slider = null): string
    {
        return $this->view('admin/hero-sliders/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->crumbs($title),
            'slider' => $slider,
            'csrf_token' => $this->csrf(),
            'action' => $slider ? '/admin/homepage-hero/' . $slider['id'] : '/admin/homepage-hero',
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function payloadWithUploadedImage(array $payload): array
    {
        $file = $_FILES['background_image_upload'] ?? null;

        if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $mediaId = $this->media->upload($file, [
                'title' => $payload['heading'] ?? 'Homepage hero slide',
                'alt_text' => $payload['heading'] ?? 'Homepage hero background',
            ], isset($this->user()['id']) ? (int) $this->user()['id'] : null);

            $media = $this->media->getById($mediaId);
            $payload['background_image'] = (string) ($media['path'] ?? '');
        }

        $payload['is_active'] = isset($payload['is_active']) ? 1 : 0;

        return $payload;
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function crumbs(string $current): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Homepage Hero Settings', 'url' => '/admin/homepage-hero'],
            ['label' => $current, 'url' => null],
        ];
    }

    private function log(string $action, string $description): void
    {
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), $action, 'homepage_hero', $description);
    }
}
