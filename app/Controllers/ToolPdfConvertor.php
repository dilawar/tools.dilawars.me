<?php

namespace App\Controllers;

use App\Data\StatsName;
use App\Data\ToolActionName;
use CodeIgniter\Exceptions\RuntimeException;

class ToolPdfConvertor extends BaseController
{
    public function index(string $what): string
    {
        return $this->loadMainView([
            'what' => $what,
        ]);
    }

    /**
     * Handle action.
     */
    public function handlePdfAction(string $action): string
    {
        log_message('info', "Handling action `$action`");
        $action = ToolActionName::from($action);

        if($action === ToolActionName::PdfConvertToJpeg) {
            return $this->convertUsingImagick();
        }

        throw new RuntimeException("Invalid PDF action $action");
    }

    private function convertUsingImagick(): string
    {
        $post = $this->request->getPost();
        log_message('debug', "post data " . json_encode($post));
        $rules = [
            'to_format' => 'required',
            'image' => [
                'uploaded[image]',
                'mime_in[image,application/pdf]',
                'max_size[image,20480]',
            ],
        ];
        if (! $this->validateData($post, $rules)) {
            return $this->loadMainView([
                'to' => $post['to_format'],
            ]);
        }

        $to = $this->request->getPost('to_format');
        if($to) {
            assert(is_string($to));
        }

        $uploadedFile = $this->request->getFile('image');
        if(! $uploadedFile) {
            return new RuntimeException("Invalid image");
        }

        $imageData = convertToUsingImagickSingle($to, $uploadedFile);

        $outFilename = $imageData->convertedFilename;

        [$pathOnDisk, $downloadUrl] = \App\Controllers\Home::writeResultFile($imageData->data, $outFilename);
        log_message('debug', "download url=$downloadUrl outfilename=$outFilename ");

        $imagick = new \Imagick($pathOnDisk);
        $imagick->thumbnailImage(256, 256, true, true);
        $thumbnail = $imagick->getImageBlob();

        StatsName::TotalImageConvcersions->increment(subkey: $to);

        return $this->loadMainView(extra: [
            'download_url' => $downloadUrl,
            'converted_file_filename' => $outFilename,
            'thumbnail' => blobToUri('jpeg', $thumbnail),
        ]);
    }

    /**
     * Convert PDF to JPGs
     *
     * @return array<string>
     */
    private function convertPdfToJpgs(string $pdffile): array 
    {
        $pdf = new \Imagick($pdffile);
        $numPages = $pdf->getNumberImages();
        $pdf->clear();
        $pdf->destroy();

        $result = [];

        for($i = 0; $i < $numPages; $i++) {
            $uri = $pdffile . '[' . $i . ']';
            $imagick = new \Imagick();
            $imagick->readImage($uri);
            $imagick->setResolution(400, 400);
            $imagick->setCompressionQuality(100);
            $imagick->sharpenImage(radius: 0, sigma: 1.0);
            $imagick->setImageFormat('jpg');
            $result[] = $imagick->getImageBlob();
            $imagick->clear();
            $imagick->destroy();
        }
        return $result;

    }

    private function loadMainView(array $extra): string 
    {
        $data = [...$extra];
        return view('tools/pdf', $data);
    }
}
