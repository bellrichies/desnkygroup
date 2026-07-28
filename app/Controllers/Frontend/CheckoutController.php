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
                'id' => $order['id'],
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
            'redirect_url' => '/shop/order-confirmation/' . rawurlencode($order['order_number']),
        ]);
    }

    public function confirmation(string $reference): string
    {
        $order = $this->authorizedOrder($reference);
        if ($order === null) {
            $this->abort(404, 'Order confirmation not found.');
        }

        return $this->view('frontend/pages/shop/confirmation', [
            'title' => 'Order Confirmation',
            'active' => 'shop',
            'order' => $order,
        ]);
    }

    public function receipt(string $reference): string
    {
        $order = $this->authorizedOrder($reference);
        if ($order === null) {
            $this->abort(404, 'Receipt not found.');
        }

        $lines = [
            'DESNKY GLOBAL RESOURCES LTD',
            'ORDER RECEIPT',
            '',
            'Order reference: ' . $order['order_number'],
            'Customer: ' . $order['customer_name'],
            'Email: ' . $order['customer_email'],
            'Phone: ' . $order['customer_phone'],
            'Delivery: ' . $order['shipping_address'] . ', ' . $order['shipping_city'] . ', ' . $order['shipping_state'],
            'Payment method: ' . ucwords(str_replace('_', ' ', (string) $order['payment_method'])),
            '',
            'ITEMS',
        ];
        foreach ($order['items'] as $item) {
            $lines[] = sprintf(
                '%dx %s - NGN %s',
                (int) $item['quantity'],
                (string) $item['product_name'],
                number_format((float) $item['line_total'], 2)
            );
        }
        $lines[] = '';
        $lines[] = 'Subtotal: NGN ' . number_format((float) $order['subtotal'], 2);
        $lines[] = 'Delivery: NGN ' . number_format((float) $order['shipping_cost'], 2);
        $lines[] = 'Total: NGN ' . number_format((float) $order['total'], 2);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="receipt-' . $order['order_number'] . '.pdf"');

        return $this->simplePdf($lines);
    }

    public function track(): string
    {
        $reference = strtoupper(trim((string) ($_GET['reference'] ?? '')));
        $order = null;
        if ($reference !== '' && preg_match('/^DGR-\d{8}-\d{4}$/', $reference)) {
            $order = $this->orders()->findByNumber($reference, false);
        }

        return $this->view('frontend/pages/shop/track', [
            'title' => 'Track Order',
            'active' => 'shop',
            'reference' => $reference,
            'order' => $order,
            'searched' => $reference !== '',
        ]);
    }

    /**
     * @return array<int, array>
     */
    private function cartItems(): array
    {
        return ShopController::contentService()->cartItems($_SESSION['cart'] ?? []);
    }

    private function orders(): OrderService
    {
        return new OrderService(new OrderRepository(DatabaseFactory::make()));
    }

    private function authorizedOrder(string $reference): ?array
    {
        $reference = strtoupper(trim($reference));
        if (!hash_equals((string) ($_SESSION['last_order']['order_number'] ?? ''), $reference)) {
            return null;
        }

        return $this->orders()->findByNumber($reference);
    }

    /**
     * Create a compact one-page PDF without adding a heavyweight runtime dependency.
     *
     * @param array<int, string> $lines
     */
    private function simplePdf(array $lines): string
    {
        $content = "BT\n/F1 10 Tf\n50 790 Td\n";
        foreach ($lines as $index => $line) {
            if ($index > 0) {
                $content .= "0 -18 Td\n";
            }
            $safe = str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ''], $line);
            $content .= '(' . substr($safe, 0, 105) . ") Tj\n";
        }
        $content .= "ET";
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>',
            '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $number => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($number + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
