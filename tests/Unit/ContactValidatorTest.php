<?php

namespace Tests\Unit;

use App\Validators\ContactValidator;
use PHPUnit\Framework\TestCase;

/**
 * ContactValidatorTest - Unit tests for contact form validation.
 */
class ContactValidatorTest extends TestCase
{
    public function testValidContactPayloadPasses(): void
    {
        $validator = new ContactValidator();

        $valid = $validator->validate([
            'full_name' => 'Ada Okafor',
            'email' => 'ada@example.com',
            'phone' => '08012345678',
            'subject' => 'Engineering enquiry',
            'message' => 'We need support with a facility engineering project.',
        ]);

        $this->assertTrue($valid);
        $this->assertSame([], $validator->errors());
    }

    public function testInvalidContactPayloadFails(): void
    {
        $validator = new ContactValidator();

        $valid = $validator->validate([
            'full_name' => '',
            'email' => 'not-an-email',
            'phone' => '123',
            'subject' => '',
            'message' => 'Short',
        ]);

        $this->assertFalse($valid);
        $this->assertArrayHasKey('full_name', $validator->errors());
        $this->assertArrayHasKey('email', $validator->errors());
        $this->assertArrayHasKey('phone', $validator->errors());
        $this->assertArrayHasKey('subject', $validator->errors());
        $this->assertArrayHasKey('message', $validator->errors());
    }
}
