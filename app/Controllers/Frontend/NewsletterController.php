<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\NewsletterRepository;
use App\Services\NewsletterService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Handles public newsletter subscriptions.
 */
class NewsletterController extends BaseController
{
    public function subscribe(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
        }

        try {
            $service = new NewsletterService(new NewsletterRepository(DatabaseFactory::make()));
            $service->subscribe([
                'email' => trim((string) ($_POST['email'] ?? '')),
                'name' => trim((string) ($_POST['name'] ?? '')),
                'source' => 'homepage',
            ]);

            return $this->json([
                'success' => true,
                'message' => 'Thank you for subscribing.',
            ]);
        } catch (\InvalidArgumentException) {
            return $this->json([
                'success' => false,
                'message' => 'Enter a valid email address.',
                'errors' => ['email' => 'Enter a valid email address.'],
            ], 422);
        } catch (Throwable $exception) {
            (new \App\Logger())->exception($exception);

            return $this->json([
                'success' => false,
                'message' => 'Subscription is temporarily unavailable. Please try again later.',
            ], 503);
        }
    }
}
