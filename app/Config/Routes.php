<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use App\Controllers\Subscription;
use App\Controllers\ToolGeo;
use App\Controllers\ToolImageCompressor;
use App\Controllers\ToolImageConvertor;
use App\Controllers\ToolPdfConvertor;

$routes->get('/', 'Home::index');

// Download files.
$routes->get('/download/(:any)', 'Downloader::index/$1');

// qr tool.
$routes->get('tool/qrcodes', 'ToolQrCodes::index');
$routes->post('tool/qrcodes/generate', 'ToolQrCodes::generate');

// Compress tools.
$routes->get('tool/compress', 'ToolImageCompressor::index');
$routes->post('tool/action/compress/(:segment)', [[ToolImageCompressor::class, 'handleAction'], '$1']);

// conversion tools.
$routes->get('/tool/convert', [[ToolImageConvertor::class, 'viewConvertTo'], '']);
$routes->get('/tool/convert/(:segment)', [[ToolImageConvertor::class, 'viewConvertTo'], '$1']);
$routes->get('/tool/convert/(:segment)/(:segment)', [[ToolImageConvertor::class, 'viewConvertTo'], '$1/$2']);

// PDF tools.
$routes->get('/tool/pdf/(:segment)', [[ToolPdfConvertor::class, 'index'], '$1']);
$routes->post('/tool/pdf/(:segment)', [[ToolPdfConvertor::class, 'handlePdfAction'], '$1']);

// conversion action
$routes->post('/tools/convertor/convert', 'ToolImageConvertor::convert');

// ocr tool
$routes->view('/tool/ocr/extract', 'tools/ocr');

// geo tools.
$routes->get('/tool/geo/map_route', [ToolGeo::class, 'viewMapRoute']);
$routes->post('/tool/geo/map_route', [ToolGeo::class, 'handleMapRoute']);

// subscription.
$routes->get('/tool/subscription/lwn', [Subscription::class, 'lwn']);

// Clock tool
$routes->view('/tool/clock', 'tools/clock.html');

service('auth')->routes($routes);
