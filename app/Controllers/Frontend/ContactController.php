<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\ContactRepository;
use App\Services\ContactService;
use App\Services\MailService;
use App\Support\DatabaseFactory;
use App\Validators\ContactValidator;
use Throwable;

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
            'active' => 'contact',
            'csrf_token' => $_SESSION['csrf_token'],
            'seo' => [
                'title' => 'Contact Desnky Global Resources Ltd',
                'description' => 'Contact Desnky Global Resources Ltd for engineering, energy, procurement, HSE, ICT and agro service enquiries in Nigeria.',
                'canonical' => 'https://www.desnkygroup.com/contact',
            ],
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
            'service_interested' => trim((string) ($_POST['service_interested'] ?? '')),
            'subject' => 'Service enquiry: ' . trim((string) ($_POST['service_interested'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
        ];

        try {
            $contactId = (new ContactService(new ContactRepository(DatabaseFactory::make())))->submit($data);
            (new MailService())->sendContactNotification($data);
        } catch (Throwable $exception) {
            (new \App\Logger())->exception($exception);

            return $this->json([
                'success' => false,
                'message' => 'We could not save your enquiry right now. Please try again shortly.',
            ], 503);
        }

        return $this->json([
            'success' => true,
            'message' => 'Thank you for contacting us! We will get back to you soon.',
            'data' => [
                'id' => $contactId,
                'email' => $data['email'],
                'received_at' => date('Y-m-d H:i:s'),
            ],
        ], 200);
    }
}
