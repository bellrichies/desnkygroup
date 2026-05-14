<?php

namespace App\Services;

use App\Repositories\OrderRepository;

/**
 * Checkout order creation business logic.
 */
class OrderService extends BaseService
{
    private OrderRepository $orders;

    public function __construct(OrderRepository $orders)
    {
        $this->orders = $orders;
    }

    /**
     * @param array $checkout Checkout payload.
     * @param array<int, array> $cartItems Cart item payloads.
     * @param array<string, int|float> $totals Cart totals.
     * @return array<string, mixed>
     */
    public function createFromCheckout(array $checkout, array $cartItems, array $totals): array
    {
        if ($cartItems === []) {
            throw new \InvalidArgumentException('Cannot create an order from an empty cart.');
        }

        $orderNumber = 'DGR-' . date('Ymd') . '-' . random_int(1000, 9999);
        $cityState = $this->splitCityState((string) $checkout['city_state']);

        $order = [
            'order_number' => $orderNumber,
            'customer_email' => strtolower(trim((string) $checkout['email'])),
            'customer_name' => trim((string) $checkout['full_name']),
            'customer_phone' => trim((string) $checkout['phone']),
            'shipping_address' => trim((string) $checkout['delivery_address']),
            'shipping_city' => $cityState['city'],
            'shipping_state' => $cityState['state'],
            'shipping_postal_code' => '',
            'subtotal' => (float) $totals['subtotal'],
            'shipping_cost' => (float) $totals['shipping'],
            'tax' => 0,
            'total' => (float) $totals['total'],
            'payment_method' => (string) $checkout['payment_method'],
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'notes' => trim((string) ($checkout['order_notes'] ?? '')),
        ];

        $items = array_map(fn (array $item): array => [
            'product_id' => $item['id'] ?? null,
            'product_name' => $item['name'],
            'unit_price' => $item['price'],
            'quantity' => $item['quantity'],
            'line_total' => $item['subtotal'],
        ], $cartItems);

        $order['id'] = $this->orders->createWithItems($order, $items);

        return $order;
    }

    /**
     * @return array{city: string, state: string}
     */
    private function splitCityState(string $cityState): array
    {
        $parts = array_map('trim', explode(',', $cityState, 2));

        return [
            'city' => $parts[0] !== '' ? $parts[0] : 'Not specified',
            'state' => $parts[1] ?? $parts[0],
        ];
    }
}
