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

use App\Data\AppQrCode;
use App\Data\StatsName;
use App\Helpers\Logger;
use Assert\Assert;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Picqer\Barcode\BarcodeGeneratorSVG;

/**
 * @var list<string>
 */
const VALID_BARCODE_TYPES = [
    'c32',
    'c39',
    'c39+',
    'c39e', // code 39 extended
    'c39e+', // code 39 extended + checksum
    'c93',
    's25',
    's25+',
    'i25',
    'i25+',
    'itf14',
    'c128',
    'c128a',
    'c128b',
    'c128c',
    'ean2', // 2-digits upc-based extention
    'ean5', // 5-digits upc-based extention
    'ean8',
    'ean13',
    'upca',
    'upce',
    'msi', // msi (variation of plessey code)
    'msi+', // msi + checksum (modulo 11)
    'postnet',
    'planet',
    'telepenalpha',
    'telepennumeric',
    'rms4cc', // rms4cc (royal mail 4-state customer code) - cbc (customer bar code)
    'kix', // kix (klant index - customer index)
    'imb', // imb - intelligent mail barcode - onecode - usps-b-3200
    'codabar',
    'code11',
    'pharma',
    'pharma2t',
];

class ToolQrCodes extends BaseController
{
    public function index(): string
    {
        return $this->loadMainView();
    }

    /**
     * Generate QR code and return as SVG.
     */
    public function generateQrImage(): ResponseInterface
    {
        $params = (array) $this->request->getGet();
        Logger::info('qr params: ', $params);

        $data = $params['data'];
        if (! $data) {
            $this->response->setStatusCode(400);

            return $this->response->setBody('Missing "data" query parameter.');
        }

        unset($params['data']);

        $appQrCode = new AppQrCode($data);
        $qrSVG = $appQrCode->svg($params);

        return $this->response
            ->setHeader('Content-Type', 'image/svg+xml;charset=utf-8')
            ->setBody($qrSVG);
    }

    public function generateBarcodeImage(): ResponseInterface
    {
        $params = (array) $this->request->getGet();
        Logger::info('barcode params: ', $params);

        $data = $params['data'];
        if (! $data) {
            $this->response->setStatusCode(400);

            return $this->response->setBody('Missing "data" query parameter.');
        }

        unset($params['data']);


        $barcodeGeneratorSVG = new BarcodeGeneratorSVG();
        $barcodeType = $params['type'] ?? 'c128';
        if (! in_array($barcodeType, VALID_BARCODE_TYPES)) {
            $this->response->setStatusCode(400);

            return $this->response->setBody(sprintf("Invalid 'type' query parameter `%s'. Valid codes are ", $barcodeType).implode(', ', VALID_BARCODE_TYPES));
        }

        $svg = '';
        try {
            $svg = $barcodeGeneratorSVG->getBarcode($data, $barcodeType);
        } catch (\Throwable $throwable) {
            $this->response->setStatusCode(400);

            return $this->response->setBody('Failed to generate barcode. Error: '.$throwable->getMessage());
        }


        return $this->response
            ->setHeader('Content-Type', 'image/svg+xml;charset=utf-8')
            ->setBody($svg);
    }

    /**
     * Generate QR codes.
     */
    public function generate(): string
    {
        /**
         * @var string
         */
        $text = $this->request->getPost('lines') ?? '';
        $data = $this->generateInner($text);

        return $this->loadMainView($data);
    }

    /**
     * @return array<string, mixed>
     */
    private function generateInner(string $text): array
    {
        Assert::that($text)->minLength(3);

        // Directory to keep generated qr codes. It must be unique.
        $resultDir = Downloader::datadir('qrcodes', hash('sha1', $text), 'qrcodes-maxflow');

        $fs = preg_split('/\R/', trim($text));
        $lines = $fs ?: [$text];

        // Rest of the parameters from POST request except lines.
        $params = (array) $this->request->getPost();
        unset($params['lines']);

        $qrcodes = [];
        $error = '';

        // Start generating HTML for generating PDF.
        $html = ['<div class="row">'];

        $i = 0;
        foreach (array_slice($lines, 0, 20) as $line) {
            ++$i;
            try {
                $qr = new AppQrCode($line);
                $qrSVG = $qr->svg($params);
                $svgFilename = $resultDir.sprintf('/qr-%d.svg', $i);
                Downloader::writeFile($svgFilename, $qrSVG);

                // log_message('debug', "QR data: " . $qrSVG);
                $svg = blobToUri($qrSVG);
                $qrcodes[] = $svg;

                $qrSizeInPx = intval($params['qr_size_in_px'] ?? '256');
                $html[] = img($svg, attributes: [
                    'width' => $qrSizeInPx,
                ]);

                StatsName::TotalQrGenerated->increment();
            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }
        }

        $html[] = '</div>';

        // Generate PDF with all qr codes.
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4');
        $dompdf->loadHtml(implode(' ', $html));
        $dompdf->render();

        $data = ['result' => $qrcodes];
        if ($pdfStr = $dompdf->output()) {
            $data['pdf'] = blobToUri($pdfStr);
        }

        $data['zip'] = Downloader::url($resultDir);
        $data['error'] = $error;

        return $data;
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
