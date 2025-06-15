<?php

namespace App\Data;

use Assert\Assert;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRImagick;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AppQrCode {
    /**
     * Generate AppQrCode
     */
    public function __construct(
        public string $data,
    ) {}

    /**
     * Generate SVG code.
     *
     * @param array<string, int|bool|string> $params
     */
    public function svg(array $params): string 
    {
        log_message('info', "Generate SVG code");

        return $this->generateSVG($this->data, $params);
    }

    /**
     * Generate QR code as svg.
     *
     * @param array<string, int|bool|string> $params
     */
    private function generateSVG(string $line, array $params): string
    {
        Assert::that($line)->minLength(2);
        log_message('info', "Genetraing qr codes for `$line` with params: " . var_export($params, true));
        $options = new QROptions();

        $qrVersion = $params['qr_version'] ?? '5';
        if($qrVersion) {
            $options->version = intval($qrVersion);
        }
        $options->outputInterface = QRImagick::class;
        $options->imageTransparent = true;
        $options->outputBase64 = false;

        if($params['circle'] ?? true) {
            $options->circleRadius = 0.45;
            $options->drawCircularModules = true;
            $options->keepAsSquare = [
                QRMatrix::M_FINDER_DARK, QRMatrix::M_FINDER_DOT, QRMatrix::M_ALIGNMENT_DARK,
            ];
        }

        $logoSpace = intval($params['qr_logo_space'] ?? '0');
        if($logoSpace > 0) {
            $options->addLogoSpace = true;
            $options->logoSpaceWidth = $logoSpace;
            $options->logoSpaceHeight = $logoSpace;
        }

        $eccLevel = $params['ecc_level'] ?? 'H';
        $options->eccLevel = EccLevel::{$eccLevel};

        return (new QRCode($options))->render($line);
    }
}
