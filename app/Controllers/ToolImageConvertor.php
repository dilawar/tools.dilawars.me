<?php

namespace App\Controllers;

use Assert\Assert;
use CodeIgniter\Exceptions\RuntimeException;
use Maestroerror\HeicToJpg;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;


class ToolImageConvertor extends BaseController
{
    public function index(string $from, string $to): string
    {
        log_message("debug", "Trying converting image $from to $to");
        if($from === 'heic') {
            return $this->loadMainView('heic', to: $to);
        }
        throw new RuntimeException("Unsupported convertion $from -> $to");
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
        if($to === "jpg" || $to === "jpeg") 
        {
            $outpath = $this->convertHeicToJpeg();
            Assert::that($outpath)->notNull();
            return $this->loadMainView('heic', 'jpeg', extra: [
                'converted_file_uri' => getDataURI($outpath),
                'converted_file_filename' => basename($outpath),
            ]);
        }
        throw new RuntimeException("Unsupported format HEIC -> $to");
    }

    private function convertHeicToJpeg(): string
    {
        $uploadedFile = $this->request->getFile('image');

        $uploadedFileTmpName = $uploadedFile->getTempName();
        $filename = $uploadedFile->getName();
        $jpgName = Path::changeExtension($filename, ".jpg");
        $outpath = storageForConvertedFile($jpgName);

        log_message('debug', "Saving the converted file to $outpath");
        HeicToJpg::convert($uploadedFileTmpName)->saveAs($outpath);
        return $outpath;
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
