<?php

namespace App\Data;

use FPDF;

class PdfFile extends FPDF 
{
    public function __construct(
        private FPDF $pdf = new FPDF()
    ) 
    {
        $this->pdf->AddPage();
    }

    public function addQrPng(string $png): void
    {
        $tempfname = tempnam(sys_get_temp_dir(), 'qr_');
        file_put_contents($tempfname, $png);

        $this->pdf->Image($tempfname, type: 'PNG');

        unlink($tempfname);
    }

    public function uri(): string 
    {
        return blobToUri($this->pdf->Output('S'));
    }
}
