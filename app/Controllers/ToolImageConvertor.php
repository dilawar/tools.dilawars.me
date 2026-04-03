<?php

namespace App\Controllers;

use App\Data\StatsName;
use App\Helpers\Logger;

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
        try {
            StatsName::TotalImageConvcersions->increment();

            return $this->convertUsingImagick();
        } catch (\Throwable $throwable) {
            setUserFlashMessage($throwable->getMessage());
            if (! isProduction()) {
                throw $throwable;
            }

            $post = (array) $this->request->getPost();
            assert(is_string($post['to_format']));

            return $this->loadMainView(
                to: $post['to_format'],
                extra: [
                    'error' => $throwable->getMessage(),
                ]
            );
        }
    }

    private function convertUsingImagick(): string
    {
        $post = (array) $this->request->getPost();
        Logger::info('post data', $post);
        $rules = array_merge(['to_format' => 'required'], $this->imageUploadRules());
        if (! $this->validateData($post, $rules)) {
            return $this->loadMainView(to: $post['to_format']);
        }

        $to = $post['to_format'];
        assert(is_string($to));

        assert($this->request instanceof \CodeIgniter\HTTP\IncomingRequest);
        $uploadedFile = $this->request->getFile('image');
        if (! $uploadedFile) {
            return new \RuntimeException('Invalid image');
        }

        $imageData = convertToUsingImagickSingle($to, $uploadedFile);
        $downloadUrl = $imageData->downloadUrl();
        Logger::debug('download url outfilename ', $downloadUrl, $imageData->convertedFilename);
        StatsName::TotalImageConvcersions->increment(subkey: $to);

        return $this->loadMainView($to, extra: [
            'download_url' => $downloadUrl,
            'converted_file_filename' => $imageData->convertedFilename,
            'thumbnail' => $imageData->thumbnailUri(),
        ]);
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadMainView(string $to, ?string $from = null, array $extra = []): string
    {
        log_message('info', sprintf('loadMainView: from=%s to=%s ', $from, $to).json_encode($extra));

        $pageTitle = 'Convert Image';
        if ('' !== $to && '0' !== $to) {
            $pageTitle .= ' To '.$to;
        }

        return view('/tools/convertor', [
            'to' => $to,
            'from' => $from,
            'page_title' => $pageTitle,
            ...$extra,
        ]);
    }
}
