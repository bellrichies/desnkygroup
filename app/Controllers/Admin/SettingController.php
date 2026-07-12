<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Repositories\ActivityLogRepository;
use App\Repositories\FaqRepository;
use App\Repositories\SiteSettingRepository;
use App\Services\ActivityLogService;
use App\Services\CacheService;
use App\Services\FaqService;
use App\Services\SiteSettingService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Admin site settings + FAQ management.
 */
class SettingController extends BaseController
{
    private SiteSettingService $settings;
    private FaqService $faqs;
    private ActivityLogService $activityLog;

    public function __construct()
    {
        $db = DatabaseFactory::make();
        $this->settings = new SiteSettingService(new SiteSettingRepository($db), new CacheService());
        $this->faqs = new FaqService(new FaqRepository($db));
        $this->activityLog = new ActivityLogService(new ActivityLogRepository($db));
    }

    public function index(): string
    {
        $tab = $_GET['tab'] ?? 'site';

        return $this->view('admin/settings/index', [
            'title' => 'Settings',
            'user' => $this->user(),
            'activeTab' => $tab,
            'fieldGroups' => $this->settings->groupedFields(),
            'values' => $this->settings->values(),
            'faqs' => $this->faqs->all(),
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [['label' => 'Settings']],
        ]);
    }

    public function update(): void
    {
        try {
            $this->settings->update(\is_array($_POST['settings'] ?? null) ? $_POST['settings'] : []);
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'settings_updated', 'settings', 'Updated site settings.');
            $this->flash('success', 'Site settings updated.');
        } catch (Throwable $e) {
            $this->flash('error', $e->getMessage());
        }

        $this->redirect('/admin/settings?tab=site');
    }

    public function clearCache(): void
    {
        $this->settings->clearCache();
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'cache_cleared', 'settings', 'Cleared application cache.');
        $this->flash('success', 'Application cache cleared.');
        $this->redirect('/admin/settings?tab=site');
    }

    // ── FAQ CRUD ────────────────────────────────────────────────────────────────

    public function faqStore(): void
    {
        try {
            $this->validate($_POST, [
                'question' => 'required|string|max:500',
                'answer' => 'required|string',
            ]);
            $id = $this->faqs->create($_POST);
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'faq_created', 'faqs', "Created FAQ #{$id}");
            $this->flash('success', 'FAQ added.');
        } catch (Throwable $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/admin/settings?tab=faqs');
    }

    public function faqEdit(string $id): string
    {
        $faq = $this->faqs->find((int) $id);
        if ($faq === null) {
            $this->abort(404, 'FAQ not found.');
        }

        return $this->view('admin/settings/faq-form', [
            'title' => 'Edit FAQ',
            'user' => $this->user(),
            'faq' => $faq,
            'csrf_token' => $this->csrf(),
            'breadcrumbs' => [
                ['label' => 'Settings', 'url' => '/admin/settings'],
                ['label' => 'Edit FAQ'],
            ],
        ]);
    }

    public function faqUpdate(string $id): void
    {
        try {
            $this->validate($_POST, [
                'question' => 'required|string|max:500',
                'answer' => 'required|string',
            ]);
            $this->faqs->update((int) $id, $_POST);
            $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'faq_updated', 'faqs', "Updated FAQ #{$id}");
            $this->flash('success', 'FAQ updated.');
        } catch (Throwable $e) {
            $this->flash('error', $e->getMessage());
        }
        $this->redirect('/admin/settings?tab=faqs');
    }

    public function faqDestroy(string $id): void
    {
        $this->faqs->delete((int) $id);
        $this->activityLog->record((int) ($this->user()['id'] ?? 0), 'faq_deleted', 'faqs', "Deleted FAQ #{$id}");
        $this->flash('success', 'FAQ deleted.');
        $this->redirect('/admin/settings?tab=faqs');
    }

    public function faqToggle(string $id): void
    {
        $this->faqs->toggleActive((int) $id);
        $this->flash('success', 'FAQ status updated.');
        $this->redirect('/admin/settings?tab=faqs');
    }
}
