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

use Assert\Assert;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRImagick;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class AppQrCode
{
    /**
     * Generate AppQrCode.
     */
    public function __construct(
        public string $data,
    ) {
    }

    /**
     * Generate SVG code.
     *
     * @param array<string, int|bool|string> $params
     */
    public function svg(array $params): string
    {
        log_message('info', 'Generate SVG code');

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
        log_message('info', sprintf('Genetraing qr codes for `%s` with params: ', $line).var_export($params, true));
        $options = new QROptions();

        $qrVersion = $params['qr_version'] ?? '5';
        if ($qrVersion) {
            $options->version = intval($qrVersion);
        }

        $options->outputInterface = QRImagick::class;
        $options->imageTransparent = true;
        $options->outputBase64 = false;

        if ($params['circle'] ?? true) {
            $options->circleRadius = 0.45;
            $options->drawCircularModules = true;
            $options->keepAsSquare = [
                QRMatrix::M_FINDER_DARK, QRMatrix::M_FINDER_DOT, QRMatrix::M_ALIGNMENT_DARK,
            ];
        }

        $logoImg = null;

        $logoSpace = intval($params['qr_logo_space'] ?? '0');
        if ($logoSpace > 0) {
            $options->addLogoSpace = true;
            $options->logoSpaceWidth = $logoSpace;
            $options->logoSpaceHeight = $logoSpace;

            // add logo.
            $qrLogoUrl = (string) $params['qr_logo_url'];
            if ('' !== $qrLogoUrl && '0' !== $qrLogoUrl) {
                log_message('debug', sprintf('Adding qr logo from %s...', $qrLogoUrl));
                try {
                    $imgContent = file_get_contents($qrLogoUrl);
                    assert(is_string($imgContent));
                    $logoImg = new \Imagick();
                    $logoImg->readImageBlob($imgContent);

                    // Not sure yet how this will correlate.
                    $logoSize = $logoSpace * $logoSpace;
                    $logoImg->resizeImage($logoSize, $logoSize, \Imagick::FILTER_LANCZOS, 0.85, true);

                } catch (\Throwable $th) {
                    log_message('error', 'failed to add logo '.$th->getMessage());
                }
            }
        }

        $eccLevel = $params['ecc_level'] ?? 'H';
        $options->eccLevel = EccLevel::{$eccLevel};

        $svgText = (new QRCode($options))->render($line);
        log_message('info', "svg:\n ".$svgText);

        if ($logoImg instanceof \Imagick) {
            return $this->addLogoToSvg($svgText, $logoImg, intval($logoSpace));
        }

        return $svgText;
    }

    private function addLogoToSvg(string $svgText, \Imagick $logoImg, int $logoSpace): string
    {
        // We insert logo as data URI inside SVG. We first parse the SVG and
        // then add the logo.
        $dom = new \DOMDocument();
        $dom->loadXML($svgText);

        $svg = $dom->getElementsByTagName('svg')[0];

        // Add a attribute with xlink as namespace.
        $namespaceAttr = $dom->createAttribute('xmlns:xlink');
        $namespaceAttr->value = 'http://www.w3.org/1999/xlink';

        $svg->appendChild($namespaceAttr);

        $logoElem = $dom->createElement('image');
        // attributes.
        $perc = intval(2 * $logoSpace);
        $xPerc = 50 - $perc / 2;
        $attrs = [
            'x' => $xPerc.'%',
            'y' => $xPerc.'%',
            'width' => $perc.'%',
            'height' => $perc.'%',
            'xlink:href' => dataUri($logoImg->getImageBlob(), 'image/png'),
        ];
        foreach ($attrs as $attrName => $attrValue) {
            $attr = $dom->createAttribute($attrName);
            $attr->value = $attrValue;
            $logoElem->appendChild($attr);
        }

        // Append logo
        $svg->appendChild($logoElem);

        return (string) $dom->saveXML();
    }
}
