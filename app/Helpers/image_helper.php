<?php

use App\Data\ImageData;
use Assert\Assert;
use CodeIgniter\HTTP\Files\UploadedFile;
use Symfony\Component\Filesystem\Path;

/**
 * Convert image to a given format
 *
 * @return string Converted image as blob.
 */
function convertToUsingImagickSingle(string $to, UploadedFile $uploadedFile): ImageData
{
    $imagick = new \Imagick();
    $imagick->readImageBlob(image: file_get_contents($uploadedFile->getTempName()));

    $uploadFilename = $uploadedFile->getName();
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
function blobToUri(string $type, string $blob): string 
{
    return 'data:' . $type . ';base64,' . base64_encode($blob);
}
