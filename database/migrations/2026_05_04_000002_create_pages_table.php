<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * CreatePagesTable - Create pages table
 * 
 * Stores CMS pages with content, metadata, and SEO information.
 */
class CreatePagesTable extends Migration
{
    public function up(): void
    {
        $this->create('pages', function ($table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('published_by', 'id', 'admin_users', 'cascade')->nullable();
            $table->foreign('created_by', 'id', 'admin_users', 'cascade');
            $table->index('slug');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        $this->dropIfExists('pages');
    }
}
