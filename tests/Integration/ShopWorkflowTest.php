<?php

namespace Tests\Integration;

use App\Controllers\Frontend\CartController;
use App\Controllers\Frontend\CheckoutController;
use PHPUnit\Framework\TestCase;

class ShopWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = ['csrf_token' => 'phase10-token'];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/shop/cart/add';
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
    }

    public function testProductCanBeAddedToCartAndQuantityIsCappedAtStock(): void
    {
        $_POST = [
            '_token' => 'phase10-token',
            'slug' => 'industrial-safety-helmet',
            'quantity' => '999',
        ];

        $response = json_decode((new CartController())->add(), true);

        $this->assertTrue($response['success']);
        $this->assertSame(35, $_SESSION['cart']['industrial-safety-helmet']);
    }

    public function testOutOfStockProductCannotBeAddedToCart(): void
    {
        $_POST = [
            '_token' => 'phase10-token',
            'slug' => 'processed-agro-pack',
            'quantity' => '1',
        ];

        $response = json_decode((new CartController())->add(), true);

        $this->assertFalse($response['success']);
        $this->assertSame('This product is currently out of stock.', $response['message']);
    }

    public function testCheckoutRejectsEmptyCart(): void
    {
        $_SERVER['REQUEST_URI'] = '/shop/checkout';
        $_POST = [
            '_token' => 'phase10-token',
        ];

        $response = json_decode((new CheckoutController())->store(), true);

        $this->assertFalse($response['success']);
        $this->assertSame('Your cart is empty.', $response['message']);
    }
}
