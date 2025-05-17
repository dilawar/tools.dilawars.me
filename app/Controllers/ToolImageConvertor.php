<?php

namespace App\Controllers;

use Assert\Assert;
use Symfony\Component\Filesystem\Path;

class ToolImageConvertor extends BaseController
{
    /**
     * Convert image to another. If format are empty, the user
     * will be asked to select format.
     */
    public function viewConvertTo(string $to = ''): string
    {
        return $this->loadMainView(to: $to);
    }

    public function convert(): string 
    {
        try {
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

        $uploadedFile = $this->request->getFile('image');
        $uploadFilename = $uploadedFile->getName();
        $outFilename = basename(Path::changeExtension($uploadFilename, ".$to"));
        log_message('debug', "Uploaded filename=$uploadFilename, result filename $outFilename");

        $imageBlob = $this->convertToUsingImagick($to, $uploadedFile);
        [$pathOnDisk, $downloadUrl] = \App\Controllers\Home::writeResultFile($imageBlob, $outFilename);
        log_message('debug', "download url=$downloadUrl outfilename=$outFilename ");

        $imagick = new \Imagick($pathOnDisk);
        $imagick->thumbnailImage(256, 256, true, true);
        $thumbnail = $imagick->getImageBlob();

        return $this->loadMainView($to, extra: [
            'download_url' => $downloadUrl,
            'converted_file_filename' => $outFilename,
            'thumbnail' => self::blobToUri('jpeg', $thumbnail),
        ]);
    }

    /**
     * Convert image blob to base64 image URI.
     */
    private static function blobToUri(string $type, string $blob): string 
    {
        return 'data:' . $type . ';base64,' . base64_encode($blob);
    }

    /**
     * Convert image blob to mime
     */
    private static function blobToMime(string $blob): string 
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($blob);
    }

    /**
     * Convert image to given format
     *
     * @return string Converted image as blob.
     */
    private function convertToUsingImagick(string $to, $uploadedFile): string
    {
        $imagick = new \Imagick();
        $imagick->readImageBlob(image: file_get_contents($uploadedFile->getTempName()));

        $res = $imagick->setImageFormat($to);
        Assert::that($res)->true();

        return $imagick->getImageBlob();
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadMainView(string $to, array $extra = []): string 
    {
        return view('/tools/convertor', [
            'to' => $to,
            ...$extra,
        ]);
    }
}
