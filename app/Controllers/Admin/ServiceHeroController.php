<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\MediaRepository;
use App\Repositories\ServiceHeroRepository;
use App\Repositories\ServiceRepository;
use App\Services\MediaService;
use App\Services\ServiceHeroService;
use App\Support\DatabaseFactory;

class ServiceHeroController extends BaseController
{
    private ServiceRepository $services;
    private ServiceHeroService $heroes;
    private MediaService $media;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->services = new ServiceRepository($db);
        $this->heroes = new ServiceHeroService(new ServiceHeroRepository($db));
        $this->media = new MediaService(new MediaRepository($db));
    }

    public function index(string $serviceId): string
    {
        $service = $this->service((int) $serviceId);

        return $this->view('admin/pages/service-heroes/index', [
            'title' => 'Hero slides · ' . $service['title'],
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs($service, 'Hero slides'),
            'service' => $service,
            'heroes' => $this->heroes->forService((int) $serviceId),
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function create(string $serviceId): string
    {
        return $this->form($this->service((int) $serviceId), 'Create hero slide');
    }

    public function store(string $serviceId): void
    {
        $this->save((int) $serviceId);
    }

    public function edit(string $serviceId, string $id): string
    {
        $service = $this->service((int) $serviceId);
        $hero = $this->heroes->find((int) $id, (int) $serviceId);
        if ($hero === null) {
            $this->abort(404, 'Hero slide not found.');
        }

        return $this->form($service, 'Edit hero slide', $hero);
    }

    public function update(string $serviceId, string $id): void
    {
        $this->save((int) $serviceId, (int) $id);
    }

    public function destroy(string $serviceId, string $id): void
    {
        $this->heroes->delete((int) $id, (int) $serviceId);
        $this->flash('success', 'Hero slide deleted.');
        $this->redirect('/admin/services/' . $serviceId . '/heroes');
    }

    public function toggle(string $serviceId, string $id): void
    {
        $this->heroes->toggle((int) $id, (int) $serviceId);
        $this->flash('success', 'Hero slide status updated.');
        $this->redirect('/admin/services/' . $serviceId . '/heroes');
    }

    public function reorder(string $serviceId): void
    {
        $this->heroes->reorder((int) $serviceId, (array) ($_POST['orders'] ?? []));
        $this->flash('success', 'Hero slide order updated.');
        $this->redirect('/admin/services/' . $serviceId . '/heroes');
    }

    private function save(int $serviceId, ?int $id = null): void
    {
        try {
            $payload = $_POST;
            $payload['service_id'] = $serviceId;
            $payload['is_visible'] = isset($_POST['is_visible']) ? 1 : 0;
            $payload['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            $payload['created_by'] = (int) ($this->user()['id'] ?? 0) ?: null;

            if (!empty($_POST['remove_background_media'])) {
                $payload['background_media'] = '';
            }

            $file = $_FILES['background_upload'] ?? null;
            if (is_array($file) && (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                if (($payload['media_type'] ?? 'image') !== 'image') {
                    throw new \InvalidArgumentException('Direct upload currently supports optimized images. Use a media URL for video.');
                }
                $mediaId = $this->media->upload($file, [
                    'title' => $payload['heading'] ?? 'Service hero',
                    'alt_text' => $payload['heading'] ?? 'Service hero background',
                ], isset($this->user()['id']) ? (int) $this->user()['id'] : null);
                $media = $this->media->getById($mediaId);
                $payload['background_media'] = (string) ($media['path'] ?? '');
            }

            if ($id === null) {
                $this->heroes->create($payload);
                $message = 'Hero slide created.';
            } else {
                $this->heroes->update($id, $serviceId, $payload);
                $message = 'Hero slide updated.';
            }

            $this->flash('success', $message);
            $this->redirect('/admin/services/' . $serviceId . '/heroes');
        } catch (\Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $destination = '/admin/services/' . $serviceId . '/heroes'
                . ($id === null ? '/create' : '/' . $id . '/edit');
            $this->redirect($destination);
        }
    }

    private function form(array $service, string $title, ?array $hero = null): string
    {
        return $this->view('admin/pages/service-heroes/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs($service, $title),
            'service' => $service,
            'hero' => $hero,
            'csrf_token' => $this->csrf(),
            'action' => '/admin/services/' . $service['id'] . '/heroes'
                . ($hero ? '/' . $hero['id'] : ''),
        ]);
    }

    private function service(int $id): array
    {
        $service = $this->services->find($id);
        if ($service === null) {
            $this->abort(404, 'Service not found.');
        }

        return $service;
    }

    private function breadcrumbs(array $service, string $current): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Services', 'url' => '/admin/services'],
            ['label' => (string) $service['title'], 'url' => '/admin/services/' . $service['id'] . '/edit'],
            ['label' => $current, 'url' => null],
        ];
    }
}
