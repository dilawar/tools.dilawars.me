<?php

use App\Controllers\ToolImageCompressor;
use App\Controllers\ToolImageConvertor;

$routes->get('/', 'Home::index');

// Download files.
$routes->get('/download/(:any)', 'Home::download/$1');

// Compress tools.
$routes->get('tool/compress', 'ToolImageCompressor::index');
$routes->post('tool/action/compress/(:segment)', [[ToolImageCompressor::class, 'handleAction'], '$1']);

// conversion tools.
$routes->get('/tool/convert', [[ToolImageConvertor::class, 'viewConvertTo'], '']);
$routes->get('/tool/convert/(:segment)', [[ToolImageConvertor::class, 'viewConvertTo'], '$1']);
$routes->get('/tool/convert/(:segment)/(:segment)', [[ToolImageConvertor::class, 'viewConvertTo'], '$1/$2']);

// conversion action
$routes->post('/tools/convertor/convert', 'ToolImageConvertor::convert');
