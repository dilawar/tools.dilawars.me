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
    ) {
    }

    /**
     * Generate download URL.
     */
    public function getDownloadUri(): void
    {
        $res = Downloader::saveImage(blob: $this->data, filename: $this->convertedFilename);
        $pathOnDisk = $res['path'];
        $downloadUrl = $res['url'];

        log_message('info', sprintf('Created donwload url %s for path %s.', $downloadUrl, $pathOnDisk));
        $this->pathOnDisk = $pathOnDisk;
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * Get download url.
     */
    public function downloadUrl(): ?string
    {
        if (! $this->downloadUrl) {
            $this->getDownloadUri();
        }

        return $this->downloadUrl;
    }

    /**
     * Return thumbnail.
     */
    public function genThumbnail(): ?string
    {
        if (! $this->pathOnDisk) {
            return null;
        }

        if ($this->thumbnail) {
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
