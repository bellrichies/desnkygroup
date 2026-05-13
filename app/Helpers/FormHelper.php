<?php

namespace App\Helpers;

/**
 * Reusable HTML form component helper.
 */
class FormHelper
{
    /**
     * Render a text input.
     *
     * @param string $name Field name.
     * @param string $value Field value.
     * @param string $placeholder Placeholder text.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    public static function textInput(
        string $name,
        string $value = '',
        string $placeholder = '',
        array $attributes = []
    ): string {
        return self::input('text', $name, $value, $placeholder, $attributes);
    }

    /**
     * Render an email input.
     *
     * @param string $name Field name.
     * @param string $value Field value.
     * @param string $placeholder Placeholder text.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    public static function emailInput(
        string $name,
        string $value = '',
        string $placeholder = '',
        array $attributes = []
    ): string {
        return self::input('email', $name, $value, $placeholder, $attributes);
    }

    /**
     * Render a textarea.
     *
     * @param string $name Field name.
     * @param string $value Field value.
     * @param int $rows Number of rows.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    public static function textarea(string $name, string $value = '', int $rows = 4, array $attributes = []): string
    {
        $attributes = array_merge([
            'id' => $name,
            'name' => $name,
            'rows' => (string) $rows,
            'class' => 'form-field',
        ], $attributes);

        return sprintf(
            '<textarea %s>%s</textarea>',
            self::attributes($attributes),
            self::escape($value)
        );
    }

    /**
     * Render a select field.
     *
     * @param string $name Field name.
     * @param array $options Select options as value => label.
     * @param string $selected Selected value.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    public static function select(
        string $name,
        array $options,
        string $selected = '',
        array $attributes = []
    ): string {
        $attributes = array_merge([
            'id' => $name,
            'name' => $name,
            'class' => 'form-field',
        ], $attributes);

        $html = sprintf('<select %s>', self::attributes($attributes));

        foreach ($options as $value => $label) {
            $optionAttributes = ['value' => (string) $value];

            if ((string) $value === $selected) {
                $optionAttributes['selected'] = true;
            }

            $html .= sprintf(
                '<option %s>%s</option>',
                self::attributes($optionAttributes),
                self::escape((string) $label)
            );
        }

        return $html . '</select>';
    }

    /**
     * Render a checkbox field with optional label.
     *
     * @param string $name Field name.
     * @param string $value Field value.
     * @param bool $checked Whether checked.
     * @param string $label Label text.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    public static function checkbox(
        string $name,
        string $value,
        bool $checked = false,
        string $label = '',
        array $attributes = []
    ): string {
        $attributes = array_merge([
            'id' => $name,
            'name' => $name,
            'type' => 'checkbox',
            'value' => $value,
            'class' => 'rounded border-gray-300 text-desnky-blue focus:ring-desnky-blue',
        ], $attributes);

        if ($checked) {
            $attributes['checked'] = true;
        }

        $input = sprintf('<input %s>', self::attributes($attributes));

        if ($label === '') {
            return $input;
        }

        return sprintf(
            '<label class="inline-flex items-start gap-3 text-sm text-desnky-ink">%s<span>%s</span></label>',
            $input,
            self::escape($label)
        );
    }

    /**
     * Render a submit button.
     *
     * @param string $text Button label.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    public static function submit(string $text = 'Submit', array $attributes = []): string
    {
        $attributes = array_merge([
            'type' => 'submit',
            'class' => 'btn-primary',
        ], $attributes);

        return sprintf('<button %s>%s</button>', self::attributes($attributes), self::escape($text));
    }

    /**
     * Render the CSRF token hidden field.
     *
     * @param string|null $token Existing token.
     * @return string
     */
    public static function token(?string $token = null): string
    {
        if ($token === null) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

            $token = $_SESSION['csrf_token'];
        }

        return sprintf(
            '<input type="hidden" name="_token" value="%s">',
            self::escape($token)
        );
    }

    /**
     * Render a validation error for a field.
     *
     * @param string $field Field name.
     * @param array $errors Error bag.
     * @return string
     */
    public static function error(string $field, array $errors = []): string
    {
        if (!isset($errors[$field])) {
            return '';
        }

        $message = is_array($errors[$field]) ? reset($errors[$field]) : $errors[$field];

        return sprintf(
            '<p class="form-error" data-error-for="%s">%s</p>',
            self::escape($field),
            self::escape((string) $message)
        );
    }

    /**
     * Render a generic input element.
     *
     * @param string $type Input type.
     * @param string $name Field name.
     * @param string $value Field value.
     * @param string $placeholder Placeholder text.
     * @param array $attributes Additional HTML attributes.
     * @return string
     */
    private static function input(
        string $type,
        string $name,
        string $value,
        string $placeholder,
        array $attributes
    ): string {
        $attributes = array_merge([
            'id' => $name,
            'name' => $name,
            'type' => $type,
            'value' => $value,
            'placeholder' => $placeholder,
            'class' => 'form-field',
        ], $attributes);

        return sprintf('<input %s>', self::attributes($attributes));
    }

    /**
     * Convert an associative array into escaped HTML attributes.
     *
     * @param array $attributes Attributes.
     * @return string
     */
    private static function attributes(array $attributes): string
    {
        $html = [];

        foreach ($attributes as $key => $value) {
            if ($value === false || $value === null) {
                continue;
            }

            if ($value === true) {
                $html[] = self::escape((string) $key);
                continue;
            }

            $html[] = sprintf('%s="%s"', self::escape((string) $key), self::escape((string) $value));
        }

        return implode(' ', $html);
    }

    /**
     * Escape HTML output.
     *
     * @param string $value Value to escape.
     * @return string
     */
    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
