<?php

declare(strict_types=1);

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * E2E tests: verifies every tool page returns HTTP 200 and renders without crashing.
 *
 * @internal
 */
final class ToolPageTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $namespace = null; // run migrations for all namespaces (App, Shield, Settings)
    protected $refresh   = false;

    public function testHomePageLoads(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);
    }

    public function testHomePageListsAllTools(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);
        $result->assertSee('QR Code Generator');
        $result->assertSee('Image Compressor');
        $result->assertSee('Image Convertor');
        $result->assertSee('PDF to JPG');
        $result->assertSee('OCR');
    }

    public function testQrCodePageLoads(): void
    {
        $result = $this->get('/tool/qrcodes');
        $result->assertStatus(200);
    }

    public function testImageConvertorPageLoads(): void
    {
        $result = $this->get('/tool/convert');
        $result->assertStatus(200);
    }

    public function testImageConvertorToJpgPageLoads(): void
    {
        $result = $this->get('/tool/convert/jpg');
        $result->assertStatus(200);
        $result->assertSee('Convert Image To jpg');
    }

    public function testImageConvertorToPngPageLoads(): void
    {
        $result = $this->get('/tool/convert/png');
        $result->assertStatus(200);
    }

    public function testImageCompressorPageLoads(): void
    {
        $result = $this->get('/tool/compress');
        $result->assertStatus(200);
    }

    public function testPdfToJpegPageLoads(): void
    {
        $result = $this->get('/tool/pdf/to_jpeg');
        $result->assertStatus(200);
    }

    public function testPdfCompressPageLoads(): void
    {
        $result = $this->get('/tool/pdf/compress');
        $result->assertStatus(200);
    }

    public function testOcrPageLoads(): void
    {
        $result = $this->get('/tool/ocr/extract');
        $result->assertStatus(200);
    }

    public function testMapRoutePageLoads(): void
    {
        $result = $this->get('/tool/geo/map_route');
        $result->assertStatus(200);
    }
}
