<?php

namespace App\Controllers;

use App\Data\ImageData;
use App\Data\StatsName;
use App\Data\ToolActionName;
use Assert\Assert;
use CodeIgniter\Exceptions\RuntimeException;
use CodeIgniter\HTTP\Files\UploadedFile;

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

        throw new RuntimeException("Invalid PDF action " . $action->value);
    }

    private function convertUsingImagick(): string
    {
        $post = (array) $this->request->getPost();
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

        $images = $this->convertPdfToJpgs($uploadedFile);
        foreach($images as &$image) {
            // store and generate download uri.
            $image->getDownloadUri();
            // generate thumbnail.
            $image->genThumbnail();
            StatsName::TotalImageConvcersions->increment(subkey: 'pdf');
        }

        return $this->loadMainView(extra: [
            'image_artifacts' => $images,
        ]);
    }

    /**
     * Convert PDF to JPGs
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
        log_message('info', "> Converting PDF file `$originalName`.");
        Assert::that($originalName)->minLength(3);

        for($i = 0; $i < $numPages; $i++) {
            $uri = $pdffile . '[' . $i . ']';
            $imagick = new \Imagick();
            $imagick->readImage($uri);
            $imagick->setResolution(400, 400);
            $imagick->setCompressionQuality(100);
            $imagick->sharpenImage(radius: 0, sigma: 1.0);
            $imagick->setImageFormat('jpg');

            $result[] = new ImageData(
                data: $imagick->getImageBlob(),
                originalFilename: $originalName,
                convertedFilename: $originalName . "-" . ($i + 1) . ".jpg",
            );

            $imagick->clear();
        }
        return $result;

    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadMainView(array $extra): string 
    {
        $data = [...$extra];
        return view('tools/pdf', $data);
    }
}
