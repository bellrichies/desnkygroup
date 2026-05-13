<?php

namespace App\Controllers;

use App\View;
use App\Exceptions\ValidationException;

/**
 * BaseController - Base class for all application controllers
 *
 * Provides common functionality like view rendering, JSON responses, redirects,
 * validation, and CSRF token handling.
 */
class BaseController
{
    /**
     * Render a view with data
     *
     * @param string $template Template path (e.g., 'frontend/pages/home')
     * @param array $data Data to pass to view
     * @return string Rendered HTML
     */
    protected function view(string $template, array $data = []): string
    {
        $view = new View();
        // Auto-detect layout based on template path
        if (strpos($template, 'frontend') === 0 || strpos($template, 'frontend/') === 0) {
            $view->setLayout('frontend/layouts/app');
        } elseif (strpos($template, 'admin') === 0 || strpos($template, 'admin/') === 0) {
            $view->setLayout('admin/layouts/app');
        }
                return $view->render($template, $data);
    }

    /**
     * Return JSON response
     *
     * @param array $data Response data
     * @param int $status HTTP status code
     * @return string JSON response
     */
    protected function json(array $data, int $status = 200): string
    {
        header('Content-Type: application/json', true, $status);
        return (string) json_encode($data);
    }

    /**
     * Redirect to URL
     *
     * @param string $url URL to redirect to
     * @param int $status HTTP status code
     * @return void
     */
    protected function redirect(string $url, int $status = 302): void
    {
        header("Location: {$url}", true, $status);
        exit;
    }

    /**
     * Abort with error response
     *
     * @param int $code HTTP status code
     * @param string $message Error message
     * @return void
     */
    protected function abort(int $code = 404, string $message = ''): void
    {
        http_response_code($code);
        echo $this->view("errors/{$code}", ['message' => $message]);
        exit;
    }

    /**
     * Validate input data
     *
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @return array Validated data
     * @throws ValidationException If validation fails
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $fieldErrors = [];

            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                $result = $this->validateRule($field, $value, $rule);
                if ($result !== true) {
                    $fieldErrors[] = $result;
                }
            }

            if (!empty($fieldErrors)) {
                $errors[$field] = $fieldErrors;
            }
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }

        return $data;
    }

    /**
     * Validate a single rule
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $rule Validation rule
     * @return bool|string True if valid, error message if invalid
     */
    private function validateRule(string $field, $value, string $rule)
    {
        // Parse rule with parameters (e.g., "max:100")
        $parts = explode(':', $rule, 2);
        $ruleName = $parts[0];
        $param = $parts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== 0 && $value !== '0') {
                    return "{$field} is required";
                }
                break;

            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "{$field} must be a valid email address";
                }
                break;

            case 'string':
                if ($value && !is_string($value)) {
                    return "{$field} must be a string";
                }
                break;

            case 'numeric':
                if ($value && !is_numeric($value)) {
                    return "{$field} must be numeric";
                }
                break;

            case 'min':
                if ($value && strlen((string) $value) < (int) $param) {
                    return "{$field} must be at least {$param} characters";
                }
                break;

            case 'max':
                if ($value && strlen((string) $value) > (int) $param) {
                    return "{$field} must not exceed {$param} characters";
                }
                break;

            case 'regex':
                if ($value && !preg_match($param, $value)) {
                    return "{$field} format is invalid";
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value && !isset($GLOBALS[$confirmField])) {
                    return "{$field} confirmation does not match";
                }
                break;
        }

        return true;
    }

    /**
     * Get or verify CSRF token
     *
     * @param string|null $token Token to verify (optional)
     * @return string|bool Token if getting, bool if verifying
     */
    protected function csrf(?string $token = null)
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        if ($token === null) {
            return $_SESSION['csrf_token'];
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Get currently authenticated admin user
     *
     * @return array|null User data if authenticated, null otherwise
     */
    protected function user(): ?array
    {
        return $_SESSION['admin_user'] ?? null;
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['admin_user']);
    }

    /**
     * Set authenticated user
     *
     * @param array $user User data
     * @return void
     */
    protected function setUser(array $user): void
    {
        $_SESSION['admin_user'] = $user;
    }

    /**
     * Clear authenticated user
     *
     * @return void
     */
    protected function clearUser(): void
    {
        unset($_SESSION['admin_user']);
    }
}
