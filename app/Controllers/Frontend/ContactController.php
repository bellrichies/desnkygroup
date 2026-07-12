<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Helpers\SeoHelper;
use App\Repositories\ContactRepository;
use App\Repositories\FaqRepository;
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

        $faqs = (new FaqRepository(DatabaseFactory::make()))->active();

        return $this->view('frontend/pages/contact', [
            'title' => 'Contact Us',
            'active' => 'contact',
            'csrf_token' => $_SESSION['csrf_token'],
            'faqs' => $faqs,
            'seo' => [
                'title' => 'Contact Desnky Global Resources Ltd',
                'description' => 'Contact Desnky Global Resources Ltd for engineering, energy, procurement, HSE, ICT and agro service enquiries in Nigeria.',
                'keywords' => 'contact Desnky Global, engineering enquiries Nigeria, procurement enquiries Lagos, HSE services Nigeria',
                'canonical' => 'https://www.desnkygroup.com/contact',
                'schema' => [
                    SeoHelper::organizationSchema(),
                    SeoHelper::localBusinessSchema(),
                    SeoHelper::contactPointSchema(),
                    SeoHelper::breadcrumbSchema([
                        'Home' => 'https://www.desnkygroup.com/',
                        'Contact' => 'https://www.desnkygroup.com/contact',
                    ]),
                ],
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

        $payload = $validator->sanitize($_POST);

        // Strip formatting characters (spaces, dashes, brackets) from the phone
        // field so entries like "0803 456 7890" pass the Nigerian number regex.
        if (isset($payload['phone'])) {
            $payload['phone'] = preg_replace('/[\s\-\(\)]/', '', $payload['phone']);
        }

        if (!$validator->validate($payload)) {
            return $this->json([
                'success' => false,
                'message' => 'Please correct the highlighted fields.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [
            'full_name' => (string) ($payload['full_name'] ?? ''),
            'email' => (string) ($payload['email'] ?? ''),
            'phone' => (string) ($payload['phone'] ?? ''),
            'company' => (string) ($payload['company'] ?? ''),
            'service_interested' => (string) ($payload['service_interested'] ?? ''),
            'subject' => 'Service enquiry: ' . (string) ($payload['service_interested'] ?? ''),
            'message' => (string) ($payload['message'] ?? ''),
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
