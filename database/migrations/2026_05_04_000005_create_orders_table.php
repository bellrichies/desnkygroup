<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * CreateOrdersTable - Create orders table
 * 
 * Stores ecommerce orders with customer and payment information.
 */
class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        $this->create('orders', function ($table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->string('customer_email', 255);
            $table->string('customer_name', 255);
            $table->string('customer_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_city', 100);
            $table->string('shipping_state', 100);
            $table->string('shipping_postal_code', 20);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method', 50); // credit_card, bank_transfer, paypal, etc.
            $table->string('payment_status', 50)->default('pending'); // pending, completed, failed, refunded
            $table->string('order_status', 50)->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->index('order_number');
            $table->index('customer_email');
            $table->index('order_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        $this->dropIfExists('orders');
    }
}
