<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\ContactRepository;
use App\Services\ActivityLogService;
use App\Support\DatabaseFactory;

class ContactController extends BaseController
{
    private ContactRepository $contacts;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->contacts = new ContactRepository($db);
        $this->activityLog = new ActivityLogService(new ActivityLogRepository($db));
    }

    public function index(): string
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'q' => $_GET['q'] ?? '',
        ];

        return $this->view('admin/contacts/index', [
            'title' => 'Contact Inquiries',
            'user' => $this->user(),
            'breadcrumbs' => [['label' => 'Contacts']],
            'contacts' => $this->contacts->filtered($filters),
            'filters' => $filters,
            'statuses' => ['new', 'read', 'responded', 'archived'],
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function show(string $id): string
    {
        $contact = $this->contacts->find((int) $id);
        if ($contact === null) {
            $this->abort(404, 'Inquiry not found.');
        }

        if ($contact['status'] === 'new') {
            $this->contacts->updateStatus((int) $id, 'read');
            $contact['status'] = 'read';
        }

        return $this->view('admin/contacts/show', [
            'title' => 'Inquiry: ' . htmlspecialchars((string) $contact['subject']),
            'user' => $this->user(),
            'breadcrumbs' => [
                ['label' => 'Contacts', 'url' => '/admin/contacts'],
                ['label' => 'View Inquiry'],
            ],
            'contact' => $contact,
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function status(string $id): void
    {
        $allowed = ['new', 'read', 'responded', 'archived'];
        $status = $_POST['status'] ?? '';

        if (!in_array($status, $allowed, true)) {
            $this->flash('error', 'Invalid status.');
            $this->redirect('/admin/contacts/' . (int) $id);
        }

        $this->contacts->updateStatus((int) $id, $status);
        $this->activityLog->record(
            (int) ($this->user()['id'] ?? 0),
            'contact_status_updated',
            'contacts',
            "Set contact #{$id} status to {$status}"
        );
        $this->flash('success', 'Inquiry status updated.');
        $this->redirect('/admin/contacts/' . (int) $id);
    }

    public function notes(string $id): void
    {
        $this->contacts->update((int) $id, [
            'status' => $_POST['status'] ?? 'read',
            'internal_notes' => $_POST['internal_notes'] ?? null,
            'assigned_to' => null,
        ]);
        $this->flash('success', 'Notes saved.');
        $this->redirect('/admin/contacts/' . (int) $id);
    }

    public function destroy(string $id): void
    {
        $this->contacts->delete((int) $id);
        $this->activityLog->record(
            (int) ($this->user()['id'] ?? 0),
            'contact_deleted',
            'contacts',
            "Deleted contact #{$id}"
        );
        $this->flash('success', 'Inquiry deleted.');
        $this->redirect('/admin/contacts');
    }
}
