<?php

namespace Tests\Unit;

use App\Validators\BaseValidator;
use PHPUnit\Framework\TestCase;

class BaseValidatorSecurityTest extends TestCase
{
    public function testSanitizeRemovesTagsAndControlCharacters(): void
    {
        $validator = new BaseValidator();

        $sanitized = $validator->sanitize([
            'name' => "  <b>Ada</b>\x00  ",
        ]);

        $this->assertSame('Ada', $sanitized['name']);
    }

    public function testSafeTextRejectsScriptPayloads(): void
    {
        $validator = new BaseValidator();

        $valid = $validator->validate([
            'message' => 'javascript:alert(1)',
        ], [
            'message' => 'required|safe_text',
        ]);

        $this->assertFalse($valid);
        $this->assertArrayHasKey('message', $validator->errors());
    }

    public function testInRuleWhitelistsAllowedValues(): void
    {
        $validator = new BaseValidator();

        $this->assertFalse($validator->validate(['service' => 'unknown'], [
            'service' => 'required|in:engineering,energy',
        ]));
    }
}
