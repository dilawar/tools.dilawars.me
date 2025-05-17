<?php

namespace App\Controllers;

use Assert\Assert;
use Symfony\Component\Filesystem\Path;

class ToolImageConvertor extends BaseController
{
    /**
     * Convert image from a given type to another. If format are empty, the user
     * will be asked to select format.
     */
    public function viewFromTo(string $from = '', string $to = ''): string
    {
        return $this->loadMainView($from, $to);
    }

    public function convert(): string 
    {
        return $this->convertFromHeic($this->request->getPost('to_format'));
    }

    private function convertFromHeic(string $to): string 
    {
        $post = $this->request->getPost();
        $rules = [
            'image' => [
                'uploaded[image]',
                'mime_in[image,image/heic,image/heif]',
            ],
        ];
        if (! $this->validateData($post, $rules)) {
            return $this->loadMainView('heic', 'jpeg');
        }

        log_message('info', "Converting with option " . json_encode($post));
        log_message('debug', "Converting HEIC image to $to...");

        [$outpath, $imageBlob] = $this->convertToUsingImagick($to);

        return $this->loadMainView('heic', $to, extra: [
            'converted_file_uri' => self::blobToUri($to, $imageBlob),
            'converted_file_filename' => basename($outpath),
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
     * Convert image to given format
     *
     * @return array{string, string} Returns name of image and image data as blob
     */
    private function convertToUsingImagick(string $to): array
    {
        $uploadedFile = $this->request->getFile('image');

        $filename = $uploadedFile->getName();
        $newName = Path::changeExtension($filename, ".$to");

        $imagick = new \Imagick();
        $imagick->readImage($uploadedFile);

        $res = $imagick->setImageFormat($to);
        Assert::that($res)->true();

        $res = $imagick->setImageFilename($newName);
        Assert::that($res)->true();

        return [$newName, $imagick->getImageBlob()];
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadMainView(string $from, string $to, array $extra = []): string 
    {
        return view('/tools/convertor', [
            'from' => $from,
            'to' => $to,
            ...$extra,
        ]);
    }
}
