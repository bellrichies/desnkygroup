<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * CreateProductsTable - Create products table
 * 
 * Stores ecommerce products with inventory and pricing.
 */
class CreateProductsTable extends Migration
{
    public function up(): void
    {
        $this->create('products', function ($table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('quantity_in_stock')->default(0);
            $table->integer('reorder_level')->default(10);
            $table->string('sku', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('category_id');
            $table->string('featured_image', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
            $table->index('slug');
            $table->index('sku');
            $table->index('category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        $this->dropIfExists('products');
    }
}
