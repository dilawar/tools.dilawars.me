<?php

namespace App\Controllers;

class ToolGeo extends BaseController
{
    public function viewMapRoute(): string
    {
        return view('tools/map_route');
    }
}
