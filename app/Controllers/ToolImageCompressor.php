<?php

namespace App\Controllers;

use App\Data\StatsName;
use App\Data\ToolActionName;
use App\Helpers\Logger;
use CodeIgniter\Exceptions\RuntimeException;
use CodeIgniter\HTTP\IncomingRequest;
use Symfony\Component\Filesystem\Path;

class ToolImageCompressor extends BaseController
{
    public function index(): string
    {
        return $this->loadToolView();
    }

    public function handleAction(string $actionName): string
    {
        Logger::info('Handling action', $actionName);
        $toolActionName = ToolActionName::from($actionName);

        if (ToolActionName::CompressImage === $toolActionName) {
            $data = $this->handleCompressImage();

            return $this->loadToolView($data);
        }

        throw new RuntimeException(sprintf('Action %s is not supported.', $actionName));
    }

    /**
     * Compress uploaded image.
     *
     * @return array<string, mixed>
     */
    private function handleCompressImage(): array
    {
        $post = (array) $this->request->getPost();
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

        assert($this->request instanceof IncomingRequest);
        $img = $this->request->getFile('image');
        assert(! is_null($img));

        $uploadFileName = $img->getName();
        $compressedImageBlob = $this->compressDefaultJpeg($img->getTempName());
        $outfile = Path::changeExtension($uploadFileName, '.jpg');

        StatsName::TotalImageCompressed->increment();

        $res = Downloader::saveImage($compressedImageBlob, $outfile);

        return [
            'download_url' => $res['url'],
            'download_filename' => basename($outfile),
            'filesize_uploaded' => $img->getSize(),
            'filesize_result' => strlen($compressedImageBlob),
        ];

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
            'page_title' => 'Image Compressor',
        ];

        return view('tools/image_compressor', $data);
    }
}
