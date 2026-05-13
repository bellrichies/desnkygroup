<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * Phase 1 service page placeholder.
 */
class ServiceController extends BaseController
{
    public function index(): string
    {
        return $this->view('frontend/pages/show', [
            'title' => 'Services',
            'content' => 'Service pages will be expanded during the frontend content phases.',
        ]);
    }

    public function show(string $slug): string
    {
        return $this->view('frontend/pages/show', [
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'content' => 'This service landing page is reserved for the next implementation phase.',
        ]);
    }
}
