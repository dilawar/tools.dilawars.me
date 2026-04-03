<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests for app_helper.php pure functions.
 *
 * @internal
 */
final class AppHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testNameToLabelConvertsUnderscoresToSpaces(): void
    {
        $this->assertSame('Foo Bar', nameToLabel('foo_bar'));
    }

    public function testNameToLabelCapitalizesFirstLetter(): void
    {
        $this->assertSame('Hello', nameToLabel('hello'));
    }

    public function testNameToLabelMultipleSegments(): void
    {
        $this->assertSame('Foo Bar Baz', nameToLabel('foo_bar_baz'));
    }

    public function testChangeExtensionReplacesExtension(): void
    {
        $this->assertSame('file.png', changeExtension('file.jpg', 'png'));
    }

    public function testChangeExtensionAcceptsLeadingDot(): void
    {
        $this->assertSame('file.png', changeExtension('file.jpg', '.png'));
    }

    public function testChangeExtensionAppendsWhenNoOriginalExtension(): void
    {
        $this->assertSame('file.jpg', changeExtension('file', 'jpg'));
    }

    public function testChangeExtensionPreservesDirectoryPath(): void
    {
        $this->assertSame('/some/path/report.pdf', changeExtension('/some/path/report.jpg', 'pdf'));
    }

    public function testBase64UrlRoundtrip(): void
    {
        $original = 'Hello, World! Special chars: +/=?&';
        $this->assertSame($original, base64_url_decode(base64_url_encode($original)));
    }

    public function testBase64UrlEncodeContainsNoUrlUnsafeChars(): void
    {
        $encoded = base64_url_encode(str_repeat('test-data-', 20));
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        $this->assertStringNotContainsString('=', $encoded);
    }

    public function testDataUriFormat(): void
    {
        $result = dataUri('hello', 'image/png');
        $this->assertSame('data:image/png;base64,' . base64_encode('hello'), $result);
    }

    public function testDataUriStartsWithDataScheme(): void
    {
        $this->assertStringStartsWith('data:', dataUri('content', 'text/plain'));
    }

    public function testNowMillisReturnsPositiveInteger(): void
    {
        $ms = now_millis();
        $this->assertIsInt($ms);
        $this->assertGreaterThan(0, $ms);
    }

    public function testNowMillisIsReasonablyRecent(): void
    {
        $ms = now_millis();
        // Should be after 2020-01-01 in milliseconds
        $this->assertGreaterThan(1577836800000, $ms);
    }

    public function testIsProductionReturnsBool(): void
    {
        $this->assertIsBool(isProduction());
    }

    public function testIsProductionIsFalseInTestEnvironment(): void
    {
        // phpunit runs with ENVIRONMENT=testing
        $this->assertFalse(isProduction());
    }
}
