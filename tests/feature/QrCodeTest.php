<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * E2E tests for QR code generation endpoints.
 *
 * @internal
 */
final class QrCodeTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $namespace = null; // run migrations for all namespaces (App, Shield, Settings)
    protected $refresh   = false;

    public function testQrSvgApiReturnsImageForValidData(): void
    {
        $result = $this->get('/qr/v1?data=https://example.com');
        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'image/svg+xml;charset=utf-8');
    }

    public function testQrSvgApiResponseContainsSvgTag(): void
    {
        $result = $this->get('/qr/v1?data=hello');
        $result->assertStatus(200);
        $result->assertSee('<svg');
    }

    public function testQrGenerateWithSingleLine(): void
    {
        $result = $this->post('/tool/qrcodes/generate', [
            'lines' => 'https://example.com',
        ]);
        $result->assertStatus(200);
    }

    public function testQrGenerateWithMultipleLines(): void
    {
        $result = $this->post('/tool/qrcodes/generate', [
            'lines' => "https://example.com\nhttps://another.com\nhttps://third.com",
        ]);
        $result->assertStatus(200);
    }

    public function testQrGenerateResponseContainsDownloadLink(): void
    {
        $result = $this->post('/tool/qrcodes/generate', [
            'lines' => 'generate-download-test',
        ]);
        $result->assertStatus(200);
        $result->assertSee('download');
    }

    public function testBarcodeSvgApiReturnsImage(): void
    {
        $result = $this->get('/barcode/v1?data=12345678&type=c128');
        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'image/svg+xml;charset=utf-8');
    }

    public function testBarcodeSvgApiWithInvalidTypeFails(): void
    {
        $result = $this->get('/barcode/v1?data=test&type=invalid_type');
        $result->assertStatus(400);
    }
}
