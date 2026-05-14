<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\OrderRepository;
use App\Services\MailService;
use App\Services\OrderService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Handles public checkout screens and first-version order capture.
 */
class CheckoutController extends BaseController
{
    public function index(): string
    {
        return $this->view('frontend/pages/shop/checkout', [
            'title' => 'Checkout',
            'active' => 'shop',
            'items' => $this->cartItems(),
            'totals' => $this->totals(),
            'csrf_token' => $this->csrf(),
            'seo' => [
                'title' => 'Checkout | Desnky Shop',
                'description' => 'Complete your Desnky Global Resources order with delivery details and payment preference.',
                'canonical' => 'https://www.desnkygroup.com/shop/checkout',
            ],
        ]);
    }

    public function store(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
        }

        if (empty($_SESSION['cart'])) {
            return $this->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $required = ['full_name', 'email', 'phone', 'delivery_address', 'city_state', 'payment_method'];
        $errors = [];

        foreach ($required as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') {
                $errors[$field] = 'This field is required.';
            }
        }

        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        }

        if ($errors !== []) {
            return $this->json([
                'success' => false,
                'message' => 'Please correct the highlighted checkout fields.',
                'errors' => $errors,
            ], 422);
        }

        try {
            $order = (new OrderService(new OrderRepository(DatabaseFactory::make())))->createFromCheckout(
                $_POST,
                $this->cartItems(),
                $this->totals()
            );
            (new MailService())->sendOrderNotification($order);
            $_SESSION['last_order'] = [
                'order_number' => $order['order_number'],
                'customer' => $order['customer_name'],
                'total' => $order['total'],
                'status' => $order['order_status'],
            ];
            $_SESSION['cart'] = [];
        } catch (Throwable $exception) {
            (new \App\Logger())->exception($exception);

            return $this->json([
                'success' => false,
                'message' => 'We could not create your order right now. Please try again shortly.',
            ], 503);
        }

        return $this->json([
            'success' => true,
            'message' => 'Order received. Our team will contact you to confirm payment and delivery.',
            'order_number' => $order['order_number'],
        ]);
    }

    /**
     * @return array<int, array>
     */
    private function cartItems(): array
    {
        $items = [];

        foreach ($_SESSION['cart'] ?? [] as $slug => $quantity) {
            $product = ShopController::products()[$slug] ?? null;

            if ($product === null) {
                continue;
            }

            $product['quantity'] = (int) $quantity;
            $product['subtotal'] = $product['quantity'] * $product['price'];
            $items[] = $product;
        }

        return $items;
    }

    /**
     * @return array<string, int>
     */
    private function totals(): array
    {
        $subtotal = array_sum(array_map(fn ($item) => $item['subtotal'], $this->cartItems()));
        $shipping = $subtotal > 0 ? 5000 : 0;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }
}
