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
            $data = $this->handleCompressImage();
            return $this->loadToolView('image_compressor', $data);
        }

        throw new RuntimeException("Action $actionName is not supported.");
    }

    /**
     * @return array<string, mixed  >
     */
    private function handleCompressImage(): array
    {
        $post = $this->request->getPost();
        $rules = [
            'image' => [
                'uploaded[image]',
                'max_size[image,20*1024]',
                'is_image[image]',
            ],
        ];

        if (! $this->validateData($post, $rules)) {
            return [];
        }

        $img = $this->request->getFile('image'); 
        $filepath = $img->store();

        log_message('debug', "File is stored to $filepath");

        $data = [
            'saved_path' => $filepath,
        ];

        return $data;

    }

    /**
     * @param array<string, mixed > $data
     */
    private function loadToolView(string $toolName, array $data = []): string 
    {
        return view("tools/$toolName", $data);
    }
}
