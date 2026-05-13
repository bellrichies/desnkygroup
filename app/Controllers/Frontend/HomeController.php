<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * HomeController - Handles public home page
 */
class HomeController extends BaseController
{
    /**
     * Display home page
     *
     * @return string
     */
    public function index(): string
    {
        return $this->view('frontend/pages/home', [
            'title' => 'Welcome to Desnky Global Resources',
            'subtitle' => 'Professional Solutions for Global Enterprises',
            'meta_description' => 'Desnky Global Resources - Professional business solutions',
        ]);
    }
}
