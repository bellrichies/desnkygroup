<?php

namespace App\Security;

/**
 * Password hashing utility.
 */
class Hasher
{
    /**
     * Hash a plain text password.
     *
     * @param string $value Plain text password.
     * @return string
     */
    public function make(string $value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * Verify a plain text password against a hash.
     *
     * @param string $value Plain text password.
     * @param string $hash Stored hash.
     * @return bool
     */
    public function check(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }

    /**
     * Determine whether a hash should be upgraded.
     *
     * @param string $hash Stored hash.
     * @return bool
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }
}
