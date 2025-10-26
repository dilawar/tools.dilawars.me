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

use App\Data\ImageData;
use App\Data\StatsName;
use App\Data\ToolActionName;
use Assert\Assert;
use CodeIgniter\Exceptions\RuntimeException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RedirectResponse;

class ToolPdfConvertor extends BaseController
{
    public function index(string $what): string
    {
        if ('compress' === $what) {
            return view('tools/pdf_compress');
        }

        if ('to_jpeg' === $what) {
            return view('tools/pdf_to_image');
        }

        return new RuntimeException(sprintf('Invalid router paramter `%s`.', $what));
    }

    /**
     * Handle action.
     */
    public function handlePdfAction(string $action): string|RedirectResponse
    {
        log_message('info', sprintf('Handling action `%s`', $action));
        $action = ToolActionName::from($action);

        $post = (array) $this->request->getPost();
        log_message('debug', 'post data '.json_encode($post));
        $rules = [
            'image' => [
                'uploaded[image]',
                'mime_in[image,application/pdf]',
                'max_size[image,20480]',
            ],
        ];
        if (! $this->validateData($post, $rules)) {
            return redirect()->back();
        }

        $uploadedFile = $this->request->getFile('image');
        if (! $uploadedFile) {
            return new RuntimeException('Invalid image');
        }

        if (ToolActionName::PdfConvertToJpeg === $action) {
            return $this->convertUsingImagick($uploadedFile);
        }

        if (ToolActionName::PdfCompress === $action) {
            return $this->compressUsingImagick($uploadedFile);
        }

        throw new RuntimeException('Invalid PDF action '.$action->value);
    }

    private function compressUsingImagick(UploadedFile $uploadedFile): string
    {
        log_message('info', 'Compressing pdf using imagick');

        $pdffile = $uploadedFile->getTempName();
        $originalName = $uploadedFile->getClientName();
        $convertedFilename = $originalName.'_compressed.pdf';

        $imagick = new \Imagick();
        $imagick->setResolution(100, 100);
        $imagick->readImage($pdffile);

        // Set image format and compression
        $imagick->setImageFormat('pdf');
        $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $imagick->setImageCompressionQuality(80); // Range: 0 (worst) to 100 (best)

        $result = new ImageData(
            data: $imagick->getImageBlob(),
            originalFilename: $originalName,
            convertedFilename: $convertedFilename,
        );

        $imagick->clear();

        return view('tools/pdf_compress', [
            'image_artifacts' => [$result],
        ]);
    }

    private function convertUsingImagick(UploadedFile $uploadedFile): string
    {
        $images = $this->convertPdfToJpgs($uploadedFile);
        foreach ($images as &$image) {
            // store and generate download uri.
            $image->getDownloadUri();
            // generate thumbnail.
            $image->genThumbnail();
            StatsName::TotalImageConvcersions->increment(subkey: 'pdf');
        }

        return view('tools/pdf_to_image', [
            'image_artifacts' => $images,
        ]);
    }

    /**
     * Convert PDF to JPGs.
     *
     * @return array<ImageData>
     */
    private function convertPdfToJpgs(UploadedFile $file): array
    {
        $pdffile = $file->getTempName();
        $pdf = new \Imagick($pdffile);
        $numPages = $pdf->getNumberImages();
        $pdf->clear();

        $result = [];

        $originalName = $file->getClientName();
        log_message('info', sprintf('> Converting PDF file `%s`.', $originalName));
        Assert::that($originalName)->minLength(3);

        for ($i = 0; $i < $numPages; ++$i) {
            $uri = $pdffile.'['.$i.']';
            $imagick = new \Imagick();
            $imagick->readImage($uri);
            $imagick->setResolution(400, 400);
            $imagick->setCompressionQuality(100);
            $imagick->sharpenImage(radius: 0, sigma: 1.0);
            $imagick->setImageFormat('jpg');

            $result[] = new ImageData(
                data: $imagick->getImageBlob(),
                originalFilename: $originalName,
                convertedFilename: $originalName.'-'.($i + 1).'.jpg',
            );

            $imagick->clear();
        }

        return $result;

    }
}
