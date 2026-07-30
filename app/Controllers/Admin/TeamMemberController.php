<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\PageRepository;
use App\Repositories\PageSectionRepository;
use App\Services\ActivityLogService;
use App\Services\TeamMemberService;
use App\Support\DatabaseFactory;

class TeamMemberController extends BaseController
{
    private TeamMemberService $team;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->team = new TeamMemberService(
            new PageRepository($db),
            new PageSectionRepository($db)
        );
        $this->activityLog = new ActivityLogService(new ActivityLogRepository($db));
    }

    public function index(): string
    {
        return $this->view('admin/team-members/index', [
            'title' => 'Team Members',
            'user' => $this->user(),
            'breadcrumbs' => $this->breadcrumbs(),
            'members' => $this->team->all(),
            'csrf_token' => $this->csrf(),
        ]);
    }

    public function store(): void
    {
        try {
            $data = $this->validatedProfile();
            $index = $this->team->create($data);
            $this->log('team_member_created', 'Created team member #' . $index . ': ' . $data['name']);
            $this->flash('success', 'Team member added successfully.');
        } catch (\Throwable $exception) {
            $this->flash('error', $this->message($exception));
        }

        $this->redirect('/admin/team-members');
    }

    public function update(string $index): void
    {
        try {
            $data = $this->validatedProfile();
            $this->team->update((int) $index, $data);
            $this->log('team_member_updated', 'Updated team member #' . $index . ': ' . $data['name']);
            $this->flash('success', 'Team member updated successfully.');
        } catch (\Throwable $exception) {
            $this->flash('error', $this->message($exception));
        }

        $this->redirect('/admin/team-members');
    }

    public function destroy(string $index): void
    {
        try {
            $this->team->delete((int) $index);
            $this->log('team_member_deleted', 'Deleted team member #' . $index);
            $this->flash('success', 'Team member removed successfully.');
        } catch (\Throwable $exception) {
            $this->flash('error', $this->message($exception));
        }

        $this->redirect('/admin/team-members');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedProfile(): array
    {
        return $this->validate($_POST, [
            'name' => 'required|string|max:120',
            'role' => 'required|string|max:120',
            'image' => 'string|max:2048',
            'image_alt' => 'string|max:255',
        ]);
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function breadcrumbs(): array
    {
        return [
            ['label' => 'Dashboard', 'url' => '/admin/dashboard'],
            ['label' => 'Team Members', 'url' => null],
        ];
    }

    private function log(string $action, string $description): void
    {
        $this->activityLog->record(
            (int) ($this->user()['id'] ?? 0),
            $action,
            'team_members',
            $description
        );
    }

    private function message(\Throwable $exception): string
    {
        return $exception instanceof \App\Exceptions\ValidationException
            ? 'Please complete the required team member fields.'
            : $exception->getMessage();
    }
}
