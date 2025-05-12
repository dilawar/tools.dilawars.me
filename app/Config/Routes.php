<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\ToolImageCompressor;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/tool/compress/image', 'ToolImageCompressor::index');
$routes->post('/tool/compress/action/(:segment)', [[ToolImageCompressor::class, 'handleAction'], '$1']);
