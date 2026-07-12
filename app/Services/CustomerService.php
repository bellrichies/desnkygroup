<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

/**
 * Builds a consolidated customer view from commerce and lead records.
 */
class CustomerService extends BaseService
{
    public function __construct(private CustomerRepository $customers)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(array $filters = []): array
    {
        $customers = [];

        foreach ($this->customers->orderRows() as $order) {
            $email = $this->emailKey((string) ($order['email'] ?? ''));
            if ($email === '') {
                continue;
            }

            $customer = &$this->ensure($customers, $email);
            $this->fillIdentity($customer, $order);
            $customer['order_count']++;
            $customer['total_spent'] += (float) ($order['total'] ?? 0);
            $customer['last_order_at'] = $this->latest($customer['last_order_at'], $order['created_at'] ?? null);
            $customer['last_activity_at'] = $this->latest($customer['last_activity_at'], $order['created_at'] ?? null);
            $customer['first_seen_at'] = $this->earliest($customer['first_seen_at'], $order['created_at'] ?? null);
            $customer['sources']['orders'] = 'Orders';
            unset($customer);
        }

        foreach ($this->customers->inquiryRows() as $inquiry) {
            $email = $this->emailKey((string) ($inquiry['email'] ?? ''));
            if ($email === '') {
                continue;
            }

            $customer = &$this->ensure($customers, $email);
            $this->fillIdentity($customer, $inquiry);
            $customer['company'] = $customer['company'] ?: (string) ($inquiry['company'] ?? '');
            $customer['inquiry_count']++;
            $customer['last_inquiry_status'] = (string) ($inquiry['status'] ?? '');
            $customer['last_inquiry_subject'] = (string) ($inquiry['subject'] ?? '');
            $customer['last_inquiry_at'] = $this->latest($customer['last_inquiry_at'], $inquiry['created_at'] ?? null);
            $customer['last_activity_at'] = $this->latest($customer['last_activity_at'], $inquiry['created_at'] ?? null);
            $customer['first_seen_at'] = $this->earliest($customer['first_seen_at'], $inquiry['created_at'] ?? null);
            $customer['sources']['inquiries'] = 'Inquiries';
            unset($customer);
        }

        foreach ($this->customers->newsletterRows() as $subscriber) {
            $email = $this->emailKey((string) ($subscriber['email'] ?? ''));
            if ($email === '') {
                continue;
            }

            $customer = &$this->ensure($customers, $email);
            $this->fillIdentity($customer, $subscriber);
            $customer['newsletter_status'] = (string) ($subscriber['status'] ?? '');
            $date = $subscriber['updated_at'] ?? $subscriber['subscribed_at'] ?? $subscriber['created_at'] ?? null;
            $customer['last_activity_at'] = $this->latest($customer['last_activity_at'], $date);
            $customer['first_seen_at'] = $this->earliest($customer['first_seen_at'], $subscriber['created_at'] ?? null);
            $customer['sources']['newsletter'] = 'Newsletter';
            unset($customer);
        }

        $result = array_values(array_map(function (array $customer): array {
            $customer['sources'] = array_values($customer['sources']);
            return $customer;
        }, $customers));

        $result = $this->filter($result, $filters);

        usort($result, static fn (array $a, array $b): int => strcmp(
            (string) ($b['last_activity_at'] ?? ''),
            (string) ($a['last_activity_at'] ?? '')
        ));

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function metrics(array $customers): array
    {
        return [
            'total' => count($customers),
            'with_orders' => count(array_filter($customers, static fn (array $row): bool => (int) $row['order_count'] > 0)),
            'with_inquiries' => count(array_filter($customers, static fn (array $row): bool => (int) $row['inquiry_count'] > 0)),
            'subscribers' => count(array_filter($customers, static fn (array $row): bool => $row['newsletter_status'] === 'subscribed')),
            'total_spent' => array_sum(array_map(static fn (array $row): float => (float) $row['total_spent'], $customers)),
        ];
    }

    /**
     * @param array<string, array<string, mixed>> $customers
     * @return array<string, mixed>
     */
    private function &ensure(array &$customers, string $email): array
    {
        if (!isset($customers[$email])) {
            $customers[$email] = [
                'email' => $email,
                'name' => '',
                'phone' => '',
                'company' => '',
                'order_count' => 0,
                'total_spent' => 0.0,
                'inquiry_count' => 0,
                'newsletter_status' => '',
                'last_order_at' => null,
                'last_inquiry_at' => null,
                'last_inquiry_status' => '',
                'last_inquiry_subject' => '',
                'first_seen_at' => null,
                'last_activity_at' => null,
                'sources' => [],
            ];
        }

        return $customers[$email];
    }

    /**
     * @param array<string, mixed> $customer
     * @param array<string, mixed> $row
     */
    private function fillIdentity(array &$customer, array $row): void
    {
        foreach (['name', 'phone'] as $field) {
            $value = trim((string) ($row[$field] ?? ''));
            if ($customer[$field] === '' && $value !== '') {
                $customer[$field] = $value;
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $customers
     * @return array<int, array<string, mixed>>
     */
    private function filter(array $customers, array $filters): array
    {
        $query = strtolower(trim((string) ($filters['q'] ?? '')));
        $segment = (string) ($filters['segment'] ?? '');

        return array_values(array_filter($customers, static function (array $customer) use ($query, $segment): bool {
            if ($query !== '') {
                $haystack = strtolower(implode(' ', [
                    $customer['name'],
                    $customer['email'],
                    $customer['phone'],
                    $customer['company'],
                    $customer['last_inquiry_subject'],
                ]));

                if (!str_contains($haystack, $query)) {
                    return false;
                }
            }

            return match ($segment) {
                'with_orders' => (int) $customer['order_count'] > 0,
                'with_inquiries' => (int) $customer['inquiry_count'] > 0,
                'newsletter' => $customer['newsletter_status'] === 'subscribed',
                default => true,
            };
        }));
    }

    private function emailKey(string $email): string
    {
        $email = strtolower(trim($email));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }

    private function latest(?string $current, mixed $candidate): ?string
    {
        $candidate = trim((string) $candidate);
        if ($candidate === '') {
            return $current;
        }

        return $current === null || strtotime($candidate) > strtotime($current) ? $candidate : $current;
    }

    private function earliest(?string $current, mixed $candidate): ?string
    {
        $candidate = trim((string) $candidate);
        if ($candidate === '') {
            return $current;
        }

        return $current === null || strtotime($candidate) < strtotime($current) ? $candidate : $current;
    }
}
