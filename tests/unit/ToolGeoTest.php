<?php

declare(strict_types=1);

namespace Tests;

use App\Controllers\ToolGeo;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ToolGeoTest extends CIUnitTestCase
{
    // --- Controller ---

    public function testControllerHasViewMapRoute(): void
    {
        $this->assertTrue(method_exists(ToolGeo::class, 'viewMapRoute'));
    }

    public function testServerSideGpxMethodsRemoved(): void
    {
        $this->assertFalse(method_exists(ToolGeo::class, 'handleMapRoute'));
        $this->assertFalse(method_exists(ToolGeo::class, 'createGpx'));
    }

    // --- Routes ---

    private function routesConfig(): string
    {
        return (string) file_get_contents(APPPATH . 'Config/Routes.php');
    }

    public function testGetRouteRegistered(): void
    {
        $this->assertStringContainsString(
            "->get('/tool/geo/map_route'",
            $this->routesConfig(),
        );
    }

    public function testPostRouteNotRegistered(): void
    {
        $this->assertStringNotContainsString(
            "->post('/tool/geo/map_route'",
            $this->routesConfig(),
        );
    }

    // --- View file ---

    private function viewContents(): string
    {
        return (string) file_get_contents(APPPATH . 'Views/tools/map_route.php');
    }

    public function testViewHasMapDiv(): void
    {
        $this->assertStringContainsString('id="map"', $this->viewContents());
    }

    public function testViewHasTimeInputs(): void
    {
        $view = $this->viewContents();
        $this->assertStringContainsString('start_time', $view);
        $this->assertStringContainsString('end_time', $view);
    }

    public function testViewHasClientSideGpxGeneration(): void
    {
        $view = $this->viewContents();
        $this->assertStringContainsString('haversineDistance', $view);
        $this->assertStringContainsString('downloadGpx', $view);
    }

    public function testViewHasNoServerSideFormSubmission(): void
    {
        $view = $this->viewContents();
        $this->assertStringNotContainsString('map_route_submit', $view);
        $this->assertStringNotContainsString('formButton.click', $view);
    }
}
