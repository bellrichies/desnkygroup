<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * Explicit stubs for routes reserved for later phases.
 */
class StubController extends BaseController
{
    public function notImplemented(): string
    {
        http_response_code(501);

        return $this->view('frontend/pages/show', [
            'title' => 'Coming Soon',
            'content' => 'This section is planned for a later implementation phase.',
        ]);
    }
}
