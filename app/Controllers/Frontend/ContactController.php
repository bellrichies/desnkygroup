<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Validators\ContactValidator;

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
            'active' => 'contact',
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

        $validator = new ContactValidator();

        if (!$validator->validate($_POST)) {
            return $this->json([
                'success' => false,
                'message' => 'Please correct the highlighted fields.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'company' => trim((string) ($_POST['company'] ?? '')),
            'subject' => trim((string) ($_POST['subject'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
        ];

        // In Phase 2+, would save to database and send email
        // For now, return success response

        return $this->json([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'data' => [
                'email' => $data['email'],
                'received_at' => date('Y-m-d H:i:s'),
            ],
        ], 200);
    }
}
