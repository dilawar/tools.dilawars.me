<?php

namespace App\Controllers;

use App\Data\StatsName;
use RuntimeException;

class ToolImageConvertor extends BaseController
{
    /**
     * Convert image to another format.
     *
     * If format are empty, the user will be asked to select format.
     */
    public function viewConvertTo(string $to = '', ?string $from = null): string
    {
        return $this->loadMainView(to: $to, from: $from);
    }

    public function convert(): string 
    {
        log_message('debug', "Converting image...");
        try {
            StatsName::TotalImageConvcersions->increment();
            return $this->convertUsingImagick();
        } catch (\Throwable $th) {
            setUserFlashMessage($th->getMessage());
            if(! isProduction()) {
                throw $th;
            }
            return $this->loadMainView(
                to: $this->request->getPost('to_format'),
                extra: [
                    'error' => $th->getMessage(),
                ]
            );
        }

    }

    private function convertUsingImagick(): string
    {
        $post = $this->request->getPost();
        log_message('debug', "post data " . json_encode($post));
        $rules = [
            'to_format' => 'required',
            'image' => [
                'uploaded[image]',
                'is_image[image]',
                'max_size[image,20480]',
            ],
        ];
        if (! $this->validateData($post, $rules)) {
            return $this->loadMainView(to: $post['to_format']);
        }

        $to = $this->request->getPost('to_format');
        assert(is_string($to));

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

        return $this->loadMainView($to, extra: [
            'download_url' => $downloadUrl,
            'converted_file_filename' => $outFilename,
            'thumbnail' => blobToUri($thumbnail),
        ]);
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadMainView(string $to, ?string $from = null, array $extra = []): string 
    {
        log_message('info', "loadMainView: from=$from to=$to " . json_encode($extra));

        $pageTitle = "Convert Image";
        if($to) {
            $pageTitle .= " To $to";
        }

        return view('/tools/convertor', [
            'to' => $to,
            'from' => $from,
            'page_title' => $pageTitle,
            ...$extra,
        ]);
    }
}
