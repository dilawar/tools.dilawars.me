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

namespace App\Controllers;

use Assert\Assert;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Symfony\Component\Filesystem\Path;

class Downloader extends BaseController
{
    /**
     * Download a file.
     */
    public function index(string ...$segs): ResponseInterface|DownloadResponse|null
    {
        $dirOrFile = join('/', $segs);
        log_message('info', "Downloading `$dirOrFile` ...");
        $dirOrFile = WRITEPATH.$dirOrFile;

        if (is_dir($dirOrFile)) {
            $filepath = $this->createZip($dirOrFile);
        } else {
            $filepath = $dirOrFile;
        }

        if (is_file($filepath)) {
            return $this->response->download($filepath, null);
        }

        return $this->response->setStatusCode(404, 'Not found');

    }

    /**
     * Save image and returns a downloadable url.
     *
     * @return array{path: string, url: string}
     */
    public static function saveImage(string $blob, string $filename): array
    {
        $sha1 = hash('sha1', $blob);

        $path = self::filepath("$sha1/$filename", 'images');
        self::writeFile($path, data: $blob);

        return [
            'path' => $path,
            'url' => self::url($path),
        ];
    }

    public static function writeFile(string $filepath, string $data): void
    {
        Assert::that($filepath)->minLength(3);
        log_message('info', "Writing data to $filepath...");
        $dirname = dirname($filepath);
        if (! is_dir($dirname)) {
            mkdir($dirname, recursive: true);
        }
        file_put_contents($filepath, $data);
    }

    /**
     * Generate download URL.
     */
    public static function url(string $subpath): string
    {
        // remove WRITEPATH from $subpath
        $path = Path::canonicalize($subpath);
        $path = Path::makeRelative($path, WRITEPATH);

        return site_url("download/$path");
    }

    /**
     * Find a directory to store result.
     *
     * @param bool $clear if TRUE, remove existing directory
     */
    public static function datadir(
        string $subdir1,
        ?string $subdir2 = null,
        ?string $subdir3 = null,
        bool $clear = true,
    ): string {
        helper('filesystem');

        $subdir = array_filter([$subdir1, $subdir2, $subdir3]);
        $dir = self::dir(join('/', $subdir));
        if (is_dir($dir) && $clear) {
            log_message('info', "Removing existing directory $dir.");
            delete_files($dir, delDir: true);
            rmdir($dir);
        }

        return $dir;
    }

    /**
     * Create a ZIP file on the fly.
     */
    private function createZip(string $dirpath): string
    {
        $now = date('Y-m-d');
        $zipFilename = Downloader::filepath($now.'-'.basename($dirpath).'.zip', 'zips');
        log_message('info', "Creating zip `$zipFilename` from directory `$dirpath`.");

        $zip = new \ZipArchive();
        $zip->open($zipFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = glob("$dirpath/*.*") ?: [];
        foreach ($files as $file) {
            log_message('debug', ">> Adding `$file` to $zipFilename");
            $zip->addFile($file, entryname: basename($file));
        }
        $zip->close();

        return $zipFilename;
    }

    private static function dir(string $subdir): string
    {
        $dir = WRITEPATH."$subdir";
        if (! is_dir($dir)) {
            log_message('info', __FUNCTION__.": creating directory $dir.");
            mkdir($dir, recursive: true);
        }

        return $dir;
    }

    private static function filepath(string $filename, string $subdir = 'generated'): string
    {
        return self::dir($subdir)."/$filename";
    }
}
