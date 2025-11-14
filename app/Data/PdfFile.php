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

class PdfFile extends \FPDF
{
    public function __construct(
        private readonly \FPDF $fpdf = new \FPDF(),
    ) {
        $this->fpdf->AddPage();
    }

    public function addQrPng(string $png): void
    {
        $tempfname = tempnam(sys_get_temp_dir(), 'qr_');
        file_put_contents($tempfname, $png);

        $this->fpdf->Image($tempfname, type: 'PNG');

        unlink($tempfname);
    }

    public function uri(): string
    {
        return blobToUri($this->fpdf->Output('S'));
    }
}
