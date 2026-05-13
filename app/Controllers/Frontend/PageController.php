<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * PageController - Handles static pages
 */
class PageController extends BaseController
{
    /**
     * Display a page by slug
     *
     * @param string $slug Page slug
     * @return string
     */
    public function show(string $slug): string
    {
        // In Phase 2+, would query database for page
        // For now, return demo content

        return $this->view('frontend/pages/show', [
            'title' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'content' => "Content for page: {$slug}",
        ]);
    }
}
