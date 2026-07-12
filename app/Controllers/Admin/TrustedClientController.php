<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\TrustedClientRepository;
use App\Repositories\ServiceRepository;
use App\Services\ActivityLogService;
use App\Services\TrustedClientService;
use App\Support\DatabaseFactory;

class TrustedClientController extends BaseController
{
    private TrustedClientService $clients;
    private ServiceRepository $services;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->clients = new TrustedClientService(new TrustedClientRepository($db));
        $this->services = new ServiceRepository($db);
        $this->activityLog = new ActivityLogService(new \App\Repositories\ActivityLogRepository($db));
    }

    public function index(): string
    {
        return $this->view('admin/trusted-clients/index', [
            'title' => 'Trusted Clients',
            'user' => $this->user(),
            'breadcrumbs' => $this->crumbs('Trusted Clients'),
            'clients' => $this->clients->all(),
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function create(): string
    {
        return $this->form('Add Trusted Client');
    }

    public function store(): void
    {
        try {
            $this->validate($_POST, ['name' => 'required|string|max:255']);
            $id = $this->clients->create($_POST);
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'trusted_client_created', 'trusted_clients', "Created trusted client #{$id}");
            $this->flash('success', 'Trusted client added.');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/admin/trusted-clients');
    }

    public function edit(string $id): string
    {
        $client = $this->clients->find((int) $id);
        if ($client === null) {
            $this->abort(404, 'Trusted client not found.');
        }

        return $this->form('Edit Trusted Client', $client);
    }

    public function update(string $id): void
    {
        try {
            $this->validate($_POST, ['name' => 'required|string|max:255']);
            $this->clients->update((int) $id, $_POST);
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'trusted_client_updated', 'trusted_clients', "Updated trusted client #{$id}");
            $this->flash('success', 'Trusted client updated.');
        } catch (\Throwable $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/admin/trusted-clients');
    }

    public function destroy(string $id): void
    {
        $this->clients->delete((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'trusted_client_deleted', 'trusted_clients', "Deleted trusted client #{$id}");
        $this->flash('success', 'Trusted client deleted.');
        $this->redirect('/admin/trusted-clients');
    }

    public function toggle(string $id): void
    {
        $this->clients->toggleActive((int) $id);
        $this->flash('success', 'Status updated.');
        $this->redirect('/admin/trusted-clients');
    }

    private function form(string $title, ?array $client = null): string
    {
        return $this->view('admin/trusted-clients/form', [
            'title' => $title,
            'user' => $this->user(),
            'breadcrumbs' => $this->crumbs($title),
            'client' => $client,
            'services' => $this->services->published(),
            'csrf_token' => $this->csrf(),
            'action' => $client ? '/admin/trusted-clients/' . $client['id'] : '/admin/trusted-clients',
        ]);
    }

    private function crumbs(string $current): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Trusted Clients', 'url' => '/admin/trusted-clients'],
            ['label' => $current, 'url' => null],
        ];
    }
}
