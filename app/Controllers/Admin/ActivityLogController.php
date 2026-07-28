<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Services\ActivityLogService;
use App\Support\DatabaseFactory;

/**
 * Activity audit log viewer.
 */
class ActivityLogController extends BaseController
{
    private ActivityLogService $activity;

    public function __construct()
    {
        $this->activity = new ActivityLogService(
            new ActivityLogRepository(DatabaseFactory::make())
        );
    }

    public function index(): string
    {
        return $this->view('admin/activity-logs/index', [
            'title' => 'Activity Logs',
            'user' => $this->user(),
            'activities' => $this->activity->filter($_GET),
            'filters' => $_GET,
            'breadcrumbs' => [['label' => 'Activity Logs']],
        ]);
    }
}
