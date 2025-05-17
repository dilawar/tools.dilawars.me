<?php

use App\Controllers\ToolImageCompressor;
use App\Controllers\ToolImageConvertor;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('tool/compress/image', 'ToolImageCompressor::index');
$routes->post('tool/compress/action/(:segment)', [[ToolImageCompressor::class, 'handleAction'], '$1']);

// conversion tools.
$routes->get('/tool/convert/(:segment)/(:segment)', [[ToolImageConvertor::class, 'index'], '$1/$2']);
$routes->post('/tools/convertor/convert', 'ToolImageConvertor::convert');
