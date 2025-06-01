<?php

namespace App\Controllers;

use Assert\Assert;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRImagick;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ToolQrCodes extends BaseController
{
    public function index(): string
    {
        return $this->loadMainView();
    }

    public function generate(): string
    {
        /**
         * @var string
         */
        $text = $this->request->getPost('lines') ?? '';
        Assert::that($text)->minLength(3);

        $fs = preg_split('/\R/', trim($text));
        if(! $fs) {
            $lines = [$text];
        } else {
            $lines = $fs;
        }

        $qrSizeValue = $this->request->getPost('qr_size') ?? '512';
        assert(is_string($qrSizeValue));

        $qrSizeInPx = intval($qrSizeValue);

        $eccLevel = $this->request->getPost('ecc_level');
        assert(is_string($eccLevel));

        log_message('info', "Genetraing qr codes for " . json_encode($lines) . " size $qrSizeInPx px.");

        $qrcodes = [];

        foreach(array_slice($lines, 0, 20) as $line) {
            $qrSVG = $this->generateQrCodeSVG($line, eccLevel: $eccLevel);
            // log_message('debug', "QR data: " . $qrSVG);
            $pngBlob = svgToPng($qrSVG, $qrSizeInPx);
            $qrcodes[] = blobToUri($pngBlob);
        }

        $data['result'] = $qrcodes;
        return $this->loadMainView($data);
    }

    /**
     * Generate QR code as svg.
     *
     * @param array<string, string> $qrOptions
     */
    private function generateQrCodeSVG(string $line, string $eccLevel, array $qrOptions = []): string
    {
        Assert::that($line)->minLength(2);

        $options = new QROptions();
        // $options->version = 7;
        $options->outputInterface = QRImagick::class;
        $options->imageTransparent = true;
        $options->outputBase64 = false;

        $options->eccLevel = EccLevel::{$eccLevel};
        $options->fromIterable($qrOptions);
        log_message('info', "Creating QRCode using options " . json_encode($options));
        return (new QRCode($options))->render($line);
    }

    /**
     * @param array<string, mixed> $extra
     */
    private function loadMainView(array $extra = []): string
    {
        $data = (array) $this->request->getPost();
        $data = array_merge($data, $extra);
        return view('tools/qrcodes', $data);
    }
}
