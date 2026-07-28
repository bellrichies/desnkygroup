<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\MediaRepository;
use App\Repositories\ServiceHeroRepository;
use App\Repositories\ServiceRepository;
use App\Services\ServiceHeroService;
use App\Support\DatabaseFactory;

class ServiceHeroController extends BaseController
{
    private ServiceRepository $services;
    private ServiceHeroService $heroes;
    private MediaRepository $media;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->services = new ServiceRepository($db);
        $this->heroes = new ServiceHeroService(new ServiceHeroRepository($db));
        $this->media = new MediaRepository($db);
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

    public function store(string $serviceId)
    {
        return $this->save((int) $serviceId);
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

    public function update(string $serviceId, string $id)
    {
        return $this->save((int) $serviceId, (int) $id);
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

    private function save(int $serviceId, ?int $id = null)
    {
        try {
            $payload = $_POST;
            $payload['service_id'] = $serviceId;
            $payload['is_visible'] = isset($_POST['is_visible']) ? 1 : 0;
            $payload['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            $payload['created_by'] = (int) ($this->user()['id'] ?? 0) ?: null;

            $payload['background_media'] = trim((string) ($payload['background_media'] ?? ''));
            if ($payload['background_media'] !== '') {
                $media = $this->media->findByPath($payload['background_media']);
                if ($media === null || !in_array($media['media_type'] ?? null, ['image', 'video'], true)) {
                    throw new \InvalidArgumentException('Select an image or video from the Media Library.');
                }
                $payload['media_type'] = (string) $media['media_type'];
            }

            if ($id === null) {
                $id = $this->heroes->create($payload);
                $message = 'Hero slide created.';
            } else {
                $this->heroes->update($id, $serviceId, $payload);
                $message = 'Hero slide updated.';
            }

            if ($this->wantsJson()) {
                return $this->json([
                    'success' => true,
                    'message' => $message,
                    'id' => $id,
                    'edit_url' => '/admin/services/' . $serviceId . '/heroes/' . $id . '/edit',
                    'update_url' => '/admin/services/' . $serviceId . '/heroes/' . $id,
                ]);
            }
            $this->flash('success', $message);
            $this->redirect('/admin/services/' . $serviceId . '/heroes');
        } catch (\Throwable $exception) {
            if ($this->wantsJson()) {
                return $this->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                ], 422);
            }
            $this->flash('error', $exception->getMessage());
            $destination = '/admin/services/' . $serviceId . '/heroes'
                . ($id === null ? '/create' : '/' . $id . '/edit');
            $this->redirect($destination);
        }
    }

    private function wantsJson(): bool
    {
        return str_contains(strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json')
            || strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    private function form(array $service, string $title, ?array $hero = null): string
    {
        $selectedMedia = null;
        if (!empty($hero['background_media'])) {
            $selectedMedia = $this->media->findByPath((string) $hero['background_media']);
        }

        return $this->view('admin/pages/service-heroes/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs($service, $title),
            'service' => $service,
            'hero' => $hero,
            'selectedMedia' => $selectedMedia,
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
