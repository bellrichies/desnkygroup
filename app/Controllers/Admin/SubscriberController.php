<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\NewsletterRepository;
use App\Services\ActivityLogService;
use App\Support\DatabaseFactory;

class SubscriberController extends BaseController
{
    private NewsletterRepository $subscribers;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->subscribers = new NewsletterRepository($db);
        $this->activityLog = new ActivityLogService(new ActivityLogRepository($db));
    }

    public function index(): string
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'q' => $_GET['q'] ?? '',
        ];

        return $this->view('admin/subscribers/index', [
            'title' => 'Newsletter Subscribers',
            'user' => $this->user(),
            'breadcrumbs' => [['label' => 'Subscribers']],
            'subscribers' => $this->subscribers->all($filters),
            'filters' => $filters,
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function unsubscribe(string $id): void
    {
        $this->subscribers->unsubscribe((int) $id);
        $this->activityLog->record(
            (int) ($this->user()['id'] ?? 0),
            'subscriber_unsubscribed',
            'subscribers',
            "Unsubscribed subscriber #{$id}"
        );
        $this->flash('success', 'Subscriber unsubscribed.');
        $this->redirect('/admin/subscribers');
    }

    public function destroy(string $id): void
    {
        $this->subscribers->delete((int) $id);
        $this->activityLog->record(
            (int) ($this->user()['id'] ?? 0),
            'subscriber_deleted',
            'subscribers',
            "Deleted subscriber #{$id}"
        );
        $this->flash('success', 'Subscriber deleted.');
        $this->redirect('/admin/subscribers');
    }

    public function export(): void
    {
        $rows = $this->subscribers->all([]);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="subscribers-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Email', 'Name', 'Source', 'Status', 'Subscribed At']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['id'],
                $row['email'],
                $row['name'] ?? '',
                $row['source'] ?? '',
                $row['status'] ?? 'subscribed',
                $row['created_at'] ?? '',
            ]);
        }
        fclose($out);
        exit;
    }
}
