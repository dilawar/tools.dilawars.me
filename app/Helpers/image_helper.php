<?php

use App\Data\ImageData;
use Assert\Assert;
use CodeIgniter\HTTP\Files\UploadedFile;
use Symfony\Component\Filesystem\Path;

/**
 * Convert image to a given format
 */
function convertToUsingImagickSingle(string $to, UploadedFile $uploadedFile): ImageData
{
    $imagick = new \Imagick();

    $content = (string) file_get_contents($uploadedFile->getTempName());
    $imagick->readImageBlob(image: $content);

    $uploadFilename = $uploadedFile->getClientName();
    $outFilename = basename(Path::changeExtension($uploadFilename, ".$to"));

    $uploadFilename = $uploadedFile->getName();
    $outFilename = basename(Path::changeExtension($uploadFilename, ".$to"));
    log_message('debug', "Uploaded filename=$uploadFilename, result filename $outFilename");

    $res = $imagick->setImageFormat($to);
    Assert::that($res)->true();
    $data = $imagick->getImageBlob();
    $imagick->clear();

    return new ImageData(
        data: $data,
        originalFilename: $uploadFilename,
        convertedFilename: $outFilename
    );
}

/**
 * Convert image blob to base64 image URI.
 */
function blobToUri(string $blob, ?string $mime = null): string 
{
    if(! $mime) {
        $finfo = new finfo(FILEINFO_MIME);
        $mime = $finfo->buffer($blob);
    }
    return 'data:' . $mime . ';base64,' . base64_encode($blob);
}

/**
 * Convert SVG to PNG.
 */
function svgToPng(string $svg, int $sizeInPx, int $resolution = 1024): string 
{
    $img = new \Imagick();
    $img->setResolution($resolution, $resolution);
    $img->readImageBlob($svg);
    $img->setImageFormat("png");
    log_message('info', "Resizing PNG to $sizeInPx x $sizeInPx.");
    $img->resizeImage($sizeInPx, $sizeInPx, \Imagick::FILTER_LANCZOS, 2);
    $data = $img->getImageBlob();
    $img->clear();

    return $data;
}
