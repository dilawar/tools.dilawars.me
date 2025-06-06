<?php

use App\Controllers\ToolImageCompressor;
use App\Controllers\ToolImageConvertor;
use App\Controllers\ToolOcr;
use App\Controllers\ToolPdfConvertor;

$routes->get('/', 'Home::index');

// Download files.
$routes->get('/download/(:any)', 'Home::download/$1');

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
$routes->get('/tool/ocr/extract', [ToolOcr::class, 'index']);
