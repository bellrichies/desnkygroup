<?php

namespace App\Validators;

/**
 * Validator for the public contact form.
 */
class ContactValidator extends BaseValidator
{
    /**
     * Validate contact form input.
     *
     * @param array $data Submitted data.
     * @param array|null $rules Optional custom rules.
     * @return bool
     */
    public function validate(array $data, ?array $rules = null): bool
    {
        return parent::validate($data, $rules ?? [
            'full_name' => 'required|string|max:150|no_html|safe_text',
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^(\+234|0)[789][01]\d{8}$/'],
            'company' => 'required|string|max:150|no_html|safe_text',
            'service_interested' => [
                'required',
                'in:engineering,energy,procurement,hse,hse-safety,ict,agro,agro-food-processing,general',
            ],
            'consent' => 'required',
            'message' => 'required|string|min:10|max:5000|no_html|safe_text',
        ]);
    }
}
