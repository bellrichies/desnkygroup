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
        $content = ShopController::contentService()->cartContent($_SESSION['cart'] ?? []);

        return $this->view('frontend/pages/shop/cart', [
            'title' => $content['title'],
            'active' => 'shop',
            'csrf_token' => $this->csrf(),
        ] + $content);
    }

    public function add(): string
    {
        if ($this->csrf($_POST['_token'] ?? null) !== true) {
            return $this->json(['success' => false, 'message' => 'Invalid security token.'], 419);
        }

        $slug = (string) ($_POST['slug'] ?? '');
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $product = ShopController::contentService()->productBySlug($slug);

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
            $product = ShopController::contentService()->productBySlug((string) $slug);

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

}
