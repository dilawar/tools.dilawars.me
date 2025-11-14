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
use Assert\Assert;
use Dompdf\Dompdf;

class ToolQrCodes extends BaseController
{
    public function index(): string
    {
        return $this->loadMainView();
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
        $html[] = '<div class="row">';

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
        $data['result'] = $qrcodes;
        if ($pdfStr = $dompdf->output()) {
            $data['pdf'] = blobToUri($pdfStr);
        }

        $data['zip'] = Downloader::url($resultDir);
        $data['error'] = $error;

        return $this->loadMainView($data);
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
