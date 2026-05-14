<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

/**
 * Session-backed shopping cart controller for public storefront pages.
 */
class CartController extends BaseController
{
    public function index(): string
    {
        return $this->view('frontend/pages/shop/cart', [
            'title' => 'Shopping Cart',
            'active' => 'shop',
            'items' => $this->cartItems(),
            'totals' => $this->totals(),
            'csrf_token' => $this->csrf(),
            'seo' => [
                'title' => 'Shopping Cart | Desnky Shop',
                'description' => 'Review selected products before checkout on the Desnky Global Resources shop.',
                'canonical' => 'https://www.desnkygroup.com/shop/cart',
            ],
        ]);
    }

    public function add(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
        }

        $slug = (string) ($_POST['slug'] ?? '');
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $product = ShopController::products()[$slug] ?? null;

        if ($product === null) {
            return $this->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        if (!$product['in_stock']) {
            return $this->json(['success' => false, 'message' => 'This product is currently out of stock.'], 422);
        }

        $_SESSION['cart'][$slug] = min(($product['stock'] ?? 1), ($_SESSION['cart'][$slug] ?? 0) + $quantity);

        return $this->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'cart_count' => array_sum($_SESSION['cart']),
        ]);
    }

    public function update(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
        }

        foreach ($_POST['quantities'] ?? [] as $slug => $quantity) {
            $product = ShopController::products()[$slug] ?? null;

            if ($product === null) {
                continue;
            }

            $quantity = max(0, min((int) $quantity, (int) $product['stock']));

            if ($quantity === 0) {
                unset($_SESSION['cart'][$slug]);
                continue;
            }

            $_SESSION['cart'][$slug] = $quantity;
        }

        return $this->json(['success' => true, 'message' => 'Cart updated.']);
    }

    public function remove(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
        }

        unset($_SESSION['cart'][(string) ($_POST['slug'] ?? '')]);

        return $this->json(['success' => true, 'message' => 'Item removed from cart.']);
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
