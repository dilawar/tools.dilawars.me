<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home');
    }

    /**
     * Download a file.
     */
    public function download(string $sha256)
    {
        $dir = WRITEPATH . 'converted/' . $sha256;
        log_message('debug', "Downloading $sha256. Searching in $dir");
        if(is_dir($dir)) {
            $files = scandir($dir);
            log_message('debug', "Directory is found. Locating first file: " . json_encode($files));
            $firstFile = $dir . '/' . $files[2]; // 0 => '.', 1 => '..', 2 => firstfile
            if(is_file($firstFile)) {
                return $this->response->download($firstFile, null);
            } else {
                log_message('warning', "No file found inside $dir.");
            }
        }

        return $this->response->setStatusCode(404, 'Not found');
    }

    /**
     * Write result file and returns downloadable url.
     *
     * @return array{string, string} filepath on disk and download url.
     */
    public static function writeResultFile(string $blob, string $filename): array
    {
        $sha1 = hash('sha1', $blob);

        // download URL only have the sha256sum of file.
        $downloadUrl = "$sha1";

        $path = WRITEPATH . "converted/$downloadUrl/$filename";
        $subdir = dirname($path);

        if(! is_dir($subdir)) {
            mkdir($subdir, recursive: true);
        }

        log_message('debug', "Writing image data to $path");
        file_put_contents($path, data: $blob);

        return [$path, site_url("/download/$downloadUrl")];
    }
}
