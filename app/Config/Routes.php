<?php

use App\Controllers\ToolImageCompressor;
use App\Controllers\ToolImageConvertor;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Download files.
$routes->get('/download/(:any)', 'Home::download/$1');

// Compress tools.
$routes->get('tool/compress/image', 'ToolImageCompressor::index');
$routes->post('tool/compress/action/(:segment)', [[ToolImageCompressor::class, 'handleAction'], '$1']);

// conversion tools.
$routes->get('/tool/convert', [[ToolImageConvertor::class, 'viewConvertTo'], '']);
$routes->get('/tool/convert/(:sengment)', [[ToolImageConvertor::class, 'viewConvertTo'], '$1']);
$routes->post('/tools/convertor/convert', 'ToolImageConvertor::convert');
