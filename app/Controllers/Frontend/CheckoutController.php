<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\MailService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Support\DatabaseFactory;
use Throwable;

/**
 * Handles public checkout screens and first-version order capture.
 */
class CheckoutController extends BaseController
{
    public function index(): string
    {
        $content = ShopController::contentService()->checkoutContent($_SESSION['cart'] ?? []);

        return $this->view('frontend/pages/shop/checkout', [
            'title' => $content['title'],
            'active' => 'shop',
            'csrf_token' => $this->csrf(),
        ] + $content);
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
            $productService = new ProductService(new ProductRepository(DatabaseFactory::make()));
            $items = $this->cartItems();
            $totals = ShopController::contentService()->totals($items);

            foreach ($items as $item) {
                if (!empty($item['id'])) {
                    $product = $productService->find((int) $item['id']);
                    if ($product === null || (int) $product['quantity_in_stock'] < (int) $item['quantity']) {
                        return $this->json([
                            'success' => false,
                            'message' => 'One or more cart items are no longer available in the requested quantity.',
                        ], 422);
                    }
                }
            }

            $order = (new OrderService(new OrderRepository(DatabaseFactory::make())))->createFromCheckout(
                $_POST,
                $items,
                $totals
            );
            foreach ($items as $item) {
                if (!empty($item['id'])) {
                    $productService->reserveInventory((int) $item['id'], (int) $item['quantity'], $order['order_number']);
                }
            }
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
        return ShopController::contentService()->cartItems($_SESSION['cart'] ?? []);
    }
}
