<?php

namespace App\Validators;

use App\Database\Connection;

/**
 * Base input validator with reusable rule parsing.
 */
class BaseValidator
{
    protected array $errors = [];
    protected array $messages = [];
    protected ?Connection $connection;

    public function __construct(?Connection $connection = null, array $messages = [])
    {
        $this->connection = $connection;
        $this->messages = $messages;
    }

    /**
     * Validate data against field rules.
     *
     * @param array $data Submitted data.
     * @param array $rules Validation rules.
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                $this->validateRule((string) $field, $value, (string) $rule);
            }
        }

        return $this->errors === [];
    }

    /**
     * Return a trimmed scalar payload with tags and control characters removed.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $value = strip_tags($value);
                $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
                $data[$key] = trim($value);
            }
        }

        return $data;
    }

    /**
     * Return validation errors.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Validate a single field rule.
     *
     * @param string $field Field name.
     * @param mixed $value Field value.
     * @param string $rule Rule string.
     * @return void
     */
    protected function validateRule(string $field, $value, string $rule): void
    {
        [$ruleName, $parameter] = array_pad(explode(':', $rule, 2), 2, null);

        if ($ruleName !== 'required' && ($value === null || $value === '')) {
            return;
        }

        switch ($ruleName) {
            case 'required':
                if ($value === null || $value === '') {
                    $this->addError($field, $ruleName, 'The :field field is required.');
                }
                break;

            case 'email':
                if (!filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $ruleName, 'The :field field must be a valid email address.');
                }
                break;

            case 'string':
                if (!is_string($value)) {
                    $this->addError($field, $ruleName, 'The :field field must be text.');
                }
                break;

            case 'min':
                if (mb_strlen((string) $value) < (int) $parameter) {
                    $this->addError($field, $ruleName, "The :field field must be at least {$parameter} characters.");
                }
                break;

            case 'max':
                if (mb_strlen((string) $value) > (int) $parameter) {
                    $this->addError($field, $ruleName, "The :field field must not exceed {$parameter} characters.");
                }
                break;

            case 'unique':
                if (!$this->isUnique($field, (string) $value, (string) $parameter)) {
                    $this->addError($field, $ruleName, 'The :field field has already been used.');
                }
                break;

            case 'regex':
                if ($parameter === null || @preg_match($parameter, (string) $value) !== 1) {
                    $this->addError($field, $ruleName, 'The :field field format is invalid.');
                }
                break;

            case 'in':
                $allowed = $parameter === null ? [] : explode(',', $parameter);
                if (!in_array((string) $value, $allowed, true)) {
                    $this->addError($field, $ruleName, 'The selected :field value is invalid.');
                }
                break;

            case 'integer':
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->addError($field, $ruleName, 'The :field field must be an integer.');
                }
                break;

            case 'boolean':
                if (!in_array($value, [true, false, 0, 1, '0', '1', 'on', 'yes'], true)) {
                    $this->addError($field, $ruleName, 'The :field field must be accepted.');
                }
                break;

            case 'url':
                if (!filter_var((string) $value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, $ruleName, 'The :field field must be a valid URL.');
                }
                break;

            case 'slug':
                if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value)) {
                    $this->addError($field, $ruleName, 'The :field field must be a valid slug.');
                }
                break;

            case 'no_html':
                if ((string) $value !== strip_tags((string) $value)) {
                    $this->addError($field, $ruleName, 'The :field field cannot contain HTML.');
                }
                break;

            case 'safe_text':
                if ($this->containsSuspiciousInput((string) $value)) {
                    $this->addError($field, $ruleName, 'The :field field contains unsupported content.');
                }
                break;
        }
    }

    /**
     * Add a validation error.
     *
     * @param string $field Field name.
     * @param string $rule Rule name.
     * @param string $message Error message.
     * @return void
     */
    protected function addError(string $field, string $rule, string $message): void
    {
        $message = $this->messages["{$field}.{$rule}"] ?? $this->messages[$field] ?? $message;
        $this->errors[$field][] = str_replace(':field', str_replace('_', ' ', $field), $message);
    }

    /**
     * Check uniqueness when a database connection is available.
     *
     * @param string $field Field name.
     * @param string $value Field value.
     * @param string $table Table name.
     * @return bool
     */
    protected function isUnique(string $field, string $value, string $table): bool
    {
        if ($this->connection === null || $table === '') {
            return true;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table) || !preg_match('/^[a-zA-Z0-9_]+$/', $field)) {
            return false;
        }

        $row = $this->connection->queryOne(
            "SELECT COUNT(*) AS total FROM {$table} WHERE {$field} = :value LIMIT 1",
            ['value' => $value]
        );

        return (int) ($row['total'] ?? 0) === 0;
    }

    private function containsSuspiciousInput(string $value): bool
    {
        $patterns = [
            '/<\s*script\b/i',
            '/javascript\s*:/i',
            '/on[a-z]+\s*=/i',
            '/\bUNION\b.+\bSELECT\b/i',
            '/\bDROP\s+TABLE\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value) === 1) {
                return true;
            }
        }

        return false;
    }
}
