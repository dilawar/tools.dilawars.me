<?php

namespace App\Controllers;

class ToolImageCompressor extends BaseController
{
    public function index(): string
    {
        return $this->loadToolView('image_compressor');
    }

    public function handleAction(string $actionName): string 
    {
        log_message("info", "Handling action $actionName...");

        return $this->loadToolView('image_compressor');
    }

    private function loadToolView(string $toolName): string {
        return view("tools/$toolName");
    }
}
