<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ToolImageCompressor extends BaseController
{
    public function index(): string
    {
        return view('tools/image_compressor');
    }
}
