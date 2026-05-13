<?php

namespace Database\Migrations;

use App\Database\Migration;

/**
 * CreateAdminUsersTable - Create admin_users table
 * 
 * Stores administrator user accounts with credentials and metadata.
 */
class CreateAdminUsersTable extends Migration
{
    public function up(): void
    {
        $this->create('admin_users', function ($table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('role', 50)->default('editor'); // super_admin, admin, editor, viewer
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('email');
            $table->index('role');
        });
    }

    public function down(): void
    {
        $this->dropIfExists('admin_users');
    }
}
