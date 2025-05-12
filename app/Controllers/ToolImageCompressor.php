<?php

namespace App\Controllers;

use App\Data\ToolActionName;
use CodeIgniter\Exceptions\RuntimeException;

class ToolImageCompressor extends BaseController
{
    public function index(): string
    {
        return $this->loadToolView('image_compressor');
    }

    public function handleAction(string $actionName): string 
    {
        log_message("info", "Handling action $actionName...");
        $action = ToolActionName::from($actionName);

        if($action === ToolActionName::CompressImage) {
            d($_FILES);
            return $this->loadToolView('image_compressor');
        }

        throw new RuntimeException("Action $actionName is not supported.");
    }

    private function loadToolView(string $toolName): string {
        return view("tools/$toolName");
    }
}
