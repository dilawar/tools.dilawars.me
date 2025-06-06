<?php

namespace App\Controllers;

class ToolOcr extends BaseController
{
    public function index(): string
    {
        return view('tools/ocr');
    }
}
