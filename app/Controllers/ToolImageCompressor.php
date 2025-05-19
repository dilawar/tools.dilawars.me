<?php

namespace App\Controllers;

use App\Data\ToolActionName;
use CodeIgniter\Exceptions\RuntimeException;
use Symfony\Component\Filesystem\Path;

class ToolImageCompressor extends BaseController
{
    public function index(): string
    {
        return $this->loadToolView();
    }

    public function handleAction(string $actionName): string 
    {
        log_message("info", "Handling action $actionName...");
        $action = ToolActionName::from($actionName);

        if($action === ToolActionName::CompressImage) {
            $data = $this->handleCompressImage();
            return $this->loadToolView($data);
        }

        throw new RuntimeException("Action $actionName is not supported.");
    }

    /**
     * @return array<string, mixed>
     */
    private function handleCompressImage(): array
    {
        $post = $this->request->getPost();
        $rules = [
            'image' => [
                'uploaded[image]',
                'max_size[image,20480]',
                'is_image[image]',
            ],
        ];

        if (! $this->validateData($post, $rules)) {
            return [];
        }

        $img = $this->request->getFile('image'); 
        assert(! is_null($img));

        $uploadFileName = $img->getName();
        $compressedImageBlob = $this->compressDefaultJpeg($img->getTempName());
        $outfile = Path::changeExtension($uploadFileName, '.jpg');

        $data = [
            'download_url' => Home::writeResultFile($compressedImageBlob, $outfile),
            'filesize_uploaded' => $img->getSize(),
            'filesize_result' => strlen($compressedImageBlob),
        ];

        return $data;

    }

    private function compressDefaultJpeg(string $filepath, int $compressionQuality = 85): string
    {
        $imagick = new \Imagick();
        $content = file_get_contents($filepath);
        assert($content);
        $imagick->readImageBlob($content);
        $imagick->setImageFormat('jpeg');

        $imagick->stripImage();
        $imagick->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
        $imagick->gaussianBlurImage(0, 0.05);
        $imagick->setImageCompressionQuality($compressionQuality);

        $blob = $imagick->getImageBlob();
        $imagick->clear();

        return $blob;
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadToolView(array $extra = []): string 
    {
        $data = [
            ...$extra,
            'page_title' => "Image Compressor",
        ];
        return view("tools/image_compressor", $data);
    }
}
