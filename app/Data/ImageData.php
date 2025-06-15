<?php

namespace App\Data;

use App\Controllers\Downloader;

class ImageData 
{
    private ?string $pathOnDisk = null;

    private ?string $downloadUrl = null;

    private ?string $thumbnail = null;

    /*
     * Create a new ImageData.
     */
    public function __construct(
        public string $data,
        public string $originalFilename,
        public string $convertedFilename,
    )
    {
    }

    /**
     * Generate download URL.
     */
    public function getDownloadUri(): void
    {
        $res = Downloader::saveImage(blob: $this->data, filename: $this->convertedFilename);
        $pathOnDisk = $res['path'];
        $downloadUrl = $res['url'];

        log_message('info', "Created donwload url $downloadUrl for path $pathOnDisk.");
        $this->pathOnDisk = $pathOnDisk;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Get download url.
     */
    public function downloadUrl(): ?string 
    {
        if(! $this->downloadUrl) {
            $this->getDownloadUri();
        }
        return $this->downloadUrl;
    }

    /**
     * Return thumbnail.
     */
    public function genThumbnail(): ?string
    {
        if(! $this->pathOnDisk) {
            return null;
        }
        if($this->thumbnail) {
            return $this->thumbnail;
        }

        $imagick = new \Imagick($this->pathOnDisk);
        $imagick->thumbnailImage(256, 256, true, true);
        $this->thumbnail = $imagick->getImageBlob();
        $imagick->clear();
        return $this->thumbnail;
    }

    public function thumbnailUri(): string 
    {
        $thumbnail = (string) $this->genThumbnail();
        return blobToUri($thumbnail);
    }
}
