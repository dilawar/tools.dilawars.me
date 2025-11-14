<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Controllers;

use App\Data\StatsName;
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
        log_message('info', sprintf('Handling action %s...', $actionName));
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
