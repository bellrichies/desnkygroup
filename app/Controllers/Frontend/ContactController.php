<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * ContactController - Handles contact form submissions
 */
class ContactController extends BaseController
{
    /**
     * Display contact form
     *
     * @return string
     */
    public function show(): string
    {
        // Generate CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $this->view('frontend/pages/contact', [
            'title' => 'Contact Us',
            'meta_description' => 'Get in touch with Desnky Global Resources',
            'csrf_token' => $_SESSION['csrf_token'],
        ]);
    }

    /**
     * Handle contact form submission
     *
     * @return string JSON response
     */
    public function submit(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid security token. Please refresh the page and try again.',
            ], 419);
        }

        // Validate input
        $data = $this->validate($_POST, [
            'full_name' => 'required|min:2|max:150',
            'email' => 'required|email',
            'phone' => 'required|min:10',
            'subject' => 'required|min:5|max:255',
            'message' => 'required|min:10|max:5000',
        ]);

        // In Phase 2+, would save to database and send email
        // For now, return success response

        return $this->json([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'data' => [
                'email' => $data['email'] ?? 'unknown',
                'received_at' => date('Y-m-d H:i:s'),
            ],
        ], 200);
    }
}
