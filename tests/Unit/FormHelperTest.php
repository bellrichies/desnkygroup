<?php

namespace Tests\Unit;

use App\Helpers\FormHelper;
use PHPUnit\Framework\TestCase;

/**
 * FormHelperTest - Unit tests for reusable form rendering helpers.
 */
class FormHelperTest extends TestCase
{
    public function testTextInputEscapesValues(): void
    {
        $html = FormHelper::textInput('full_name', '<b>Ada</b>', 'Name');

        $this->assertStringContainsString('name="full_name"', $html);
        $this->assertStringContainsString('&lt;b&gt;Ada&lt;/b&gt;', $html);
    }

    public function testSelectMarksSelectedOption(): void
    {
        $html = FormHelper::select('service', [
            'engineering' => 'Engineering',
            'ict' => 'ICT',
        ], 'ict');

        $this->assertStringContainsString('value="ict" selected', $html);
    }

    public function testErrorRendersFirstFieldError(): void
    {
        $html = FormHelper::error('email', [
            'email' => ['The email field is required.'],
        ]);

        $this->assertStringContainsString('data-error-for="email"', $html);
        $this->assertStringContainsString('The email field is required.', $html);
    }
}
