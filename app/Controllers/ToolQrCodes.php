<?php

namespace App\Controllers;

use Assert\Assert;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRImagick;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;

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

        $params = (array) $this->request->getPost();
        unset($params['lines']);
        log_message('info', "Genetraing qr codes for " . json_encode($lines) . " params: " . var_export($params, true));

        $qrcodes = [];
        $error = '';

        $html[] = '<div class="row">';

        foreach(array_slice($lines, 0, 20) as $line) {
            try {
                $qrSVG = $this->generateQrCodeSVG($line, params: $params);
                // log_message('debug', "QR data: " . $qrSVG);
                $svg = blobToUri($qrSVG);
                $qrcodes[] = $svg;

                $qrSizeInPx = intval($params['qr_size_in_px'] ?? '256');
                $html[] = img($svg, attributes: [
                    'width' => $qrSizeInPx,
                ]);

            } catch (\Throwable $th) {
                $error = $th->getMessage();
            }
        }
        $html[] = '</div>';

        $pdf = new Dompdf();
        $pdf->setPaper('A4');
        $pdf->loadHtml(implode(' ', $html));
        $pdf->render();

        $data['result'] = $qrcodes;
        if($pdfStr = $pdf->output()) {
            $data['pdf'] = blobToUri($pdfStr);
        }
        $data['error'] = $error;
        return $this->loadMainView($data);
    }

    /**
     * Generate QR code as svg.
     *
     * @param array<string, int|bool|string> $params
     */
    private function generateQrCodeSVG(string $line, array $params): string
    {
        Assert::that($line)->minLength(2);

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
            $options->keepAsSquare = [QRMatrix::M_FINDER_DARK, QRMatrix::M_FINDER_DOT, QRMatrix::M_ALIGNMENT_DARK];
        }

        $logoSpace = intval($params['qr_logo_space'] ?? '0');
        if($logoSpace > 0) {
            $options->addLogoSpace = true;
            $options->logoSpaceWidth = $logoSpace;
            $options->logoSpaceHeight = $logoSpace;
        }

        $eccLevel = $params['ecc_level'] ?? 'H';
        $options->eccLevel = EccLevel::{$eccLevel};

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
