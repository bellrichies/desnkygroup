<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * CreateContactsTable - Create contacts table
 * 
 * Stores contact form submissions and inquiries.
 */
class CreateContactsTable extends Migration
{
    public function up(): void
    {
        $this->create('contacts', function ($table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('email', 255);
            $table->string('phone', 20)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('subject', 255);
            $table->text('message');
            $table->string('source', 50)->default('contact_form'); // contact_form, email, phone, etc.
            $table->string('status', 50)->default('new'); // new, read, responded, closed
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->index('email');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        $this->dropIfExists('contacts');
    }
}
