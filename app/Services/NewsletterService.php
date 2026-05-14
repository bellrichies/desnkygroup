<?php

namespace App\Services;

use App\Repositories\NewsletterRepository;
use App\Validators\BaseValidator;

/**
 * Business logic for newsletter subscriptions.
 */
class NewsletterService extends BaseService
{
    private NewsletterRepository $newsletter;

    public function __construct(NewsletterRepository $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    /**
     * @param array $data Subscriber payload.
     * @return int Subscriber ID.
     */
    public function subscribe(array $data): int
    {
        $validator = new BaseValidator();

        if (
            !$validator->validate($data, [
                'email' => 'required|email',
                'name' => 'string|max:150',
            ])
        ) {
            throw new \InvalidArgumentException('Invalid newsletter payload.');
        }

        return $this->newsletter->subscribe(
            strtolower(trim((string) $data['email'])),
            isset($data['name']) ? trim((string) $data['name']) : null,
            (string) ($data['source'] ?? 'homepage')
        );
    }
}
