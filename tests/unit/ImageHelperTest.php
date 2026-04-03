<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests for image_helper.php pure functions.
 *
 * @internal
 */
final class ImageHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('image');
    }

    public function testBlobToUriWithExplicitMime(): void
    {
        $content = 'fake image data';
        $result = blobToUri($content, 'image/png');
        $this->assertSame('data:image/png;base64,' . base64_encode($content), $result);
    }

    public function testBlobToUriStartsWithDataScheme(): void
    {
        $this->assertStringStartsWith('data:', blobToUri('any content', 'image/jpeg'));
    }

    public function testBlobToUriContainsBase64Segment(): void
    {
        $this->assertStringContainsString(';base64,', blobToUri('content', 'image/gif'));
    }

    public function testBlobToUriAutoDetectsMimeFromContent(): void
    {
        // Minimal valid PNG: 8-byte signature
        $pngSignature = "\x89PNG\r\n\x1a\n";
        $result = blobToUri($pngSignature);
        $this->assertStringStartsWith('data:', $result);
        $this->assertStringContainsString(';base64,', $result);
    }

    public function testBlobToUriEncodesContentAsBase64(): void
    {
        $content = 'hello world';
        $result = blobToUri($content, 'text/plain');
        $this->assertStringContainsString(base64_encode($content), $result);
    }
}
