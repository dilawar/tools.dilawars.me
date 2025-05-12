<?php

namespace App\Controllers;

class ToolImageCompressor extends BaseController
{
    public function index(): string
    {
        return view('tools/image_compressor');
    }
}
