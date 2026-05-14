<?php

namespace App\Models;

/**
 * Admin user model.
 */
class AdminUser extends BaseModel
{
    public int $id;
    public string $fullName;
    public string $email;
    public string $passwordHash;
    public string $role;
    public bool $isActive;
    public ?string $lastLoginAt;

    /**
     * @param array<string, mixed> $attributes Admin user attributes.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->id = (int) ($attributes['id'] ?? 0);
        $this->fullName = (string) ($attributes['full_name'] ?? '');
        $this->email = (string) ($attributes['email'] ?? '');
        $this->passwordHash = (string) ($attributes['password_hash'] ?? '');
        $this->role = (string) ($attributes['role'] ?? 'editor');
        $this->isActive = (bool) ($attributes['is_active'] ?? false);
        $this->lastLoginAt = $attributes['last_login_at'] ?? null;
    }

    /**
     * Verify a plain password against this user's stored hash.
     *
     * @param string $password Plain text password.
     * @return bool
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }

    /**
     * Update the in-memory last login timestamp.
     *
     * @param string|null $timestamp Timestamp string.
     * @return void
     */
    public function updateLastLogin(?string $timestamp = null): void
    {
        $this->lastLoginAt = $timestamp ?: date('Y-m-d H:i:s');
        $this->attributes['last_login_at'] = $this->lastLoginAt;
    }

    /**
     * Session-safe payload for authenticated admin state.
     *
     * @return array<string, mixed>
     */
    public function toSessionArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'full_name' => $this->fullName,
            'role' => $this->role,
            'last_login_at' => $this->lastLoginAt,
        ];
    }
}
