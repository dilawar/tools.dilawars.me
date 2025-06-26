<?php

echo $this->extend('default');
echo $this->section('content');

$lines = $lines ?? 'https://tools.maxflow.in';

// result as pdf file.
$qrCodesAsPdf = $pdf ?? '';
// results as zip file.
$qrCodesAsZip = $zip ?? '';

// base64 encoded QR codes.
$qrCodesBase64 = $result ?? [];

$error = $error ?? null;
$eccLevel = $ecc_level ?? 'H';
$qrSizeInPx = $qr_size_in_px ?? '256';
$qrLogoSpace = $qr_logo_space ?? '10';
$qrLogoUrl = $qr_logo_url ?? '';
$qrVersion = $qr_version ?? '5';

if(! function_exists('renderQrForm')) {

    /**
     * @param array<string, string> $params
     */
    function renderQrForm(string $lines, array $params = []): string 
    {
        $qrSizeInPx = $params['qr_size_in_px'] ?? '256';
        $qrVersion = $params['qr_version'] ?? '5';
        $qrLogoSpace = $params['qr_logo_space'] ?? '10';
        $qrLogoUrl = $params['qr_logo_url'] ?? '10';

        $html[] = "<div>";
        $html[] = "<details class='mt-1'>
            <summary style='float:right'>Help</summary>
            <ul>
                <li>
                    <strong>ECC</strong> is the error correction level. High level of ECC
                    makes your QR code readable (for a cost of larger size) even with some 
                    damages to prints.
                </li>
            </ul>
        </details>";

        // Row for textarea
        $html[] = "<div class='form-label text-info'>
            Each line will be converted to a QR code. You can type a maximum 20 lines.
        </div>";
        $html[] = "<div class='row'>";
        $html[] = "<div class='col-10'>";
        $html[] = form_textarea("lines", $lines, extra: [
            'class' => 'form-control',
            'rows' => "4",
        ]);
        $html[] = "</div>";
        $html[] = "</div>";

        $html[] = "<div class='h4 mt-2'>QR Options</div>";

        // Row for size.
        $html[] = formInputBootstrap('qr_size_in_px', label: "QR Size (px)", value: $qrSizeInPx, type: 'number');

        // row for version.
        $html[] = formInputBootstrap('qr_version', label: "QR Version", value: $qrVersion, type: 'number');

        // Row for select.
        $options = [
            'L' => 'L',
            'M' => 'M',
            'Q' => 'Q',
            'H' => 'H',
        ];
        $html[] = formSelectBootstrap(
            'ecc_level',
            label: "ECC (Error Correction) Level",
            value: $params['ecc_level'] ?? 'M',
            options: $options,
        );

        // logo space.
        $html[] = formInputBootstrap(
            'qr_logo_space',
            label: "Logo Space (typically between 10 and 25)",
            value: $qrLogoSpace,
            type: 'number'
        );

        // logo url.
        $html[] = formInputBootstrap(
            'qr_logo_url',
            label: "Logo URL",
            value: $qrLogoUrl,
            type: 'text',
        );

        // Row for the submit button.
        $html[] = "<div class='row d-flex justify-content-end mt-3'>";
        $html[] = "<div class='col-6'>";
        $html[] = form_submit('submit', "Generate", extra: [
            'class' => 'btn btn-primary col-6',
        ]);
        $html[] = "</div>";
        $html[] = "</div>";

        $html[] = "</div>";

        $html[] = "</div>";

        return implode(' ', $html);
    }
}

?>

<section>
<h4 class="section-title">QR Code Generator</h4>

<?php echo form_open('/tool/qrcodes/generate');
echo '<p>
    You can generate multiple QR codes (upto 20) with or without your logo.
    Download them as ZIP or PDF file.
</p>';

echo renderQrForm($lines, params: [
    'qr_size_in_px' => $qrSizeInPx,
    'ecc_level' => $eccLevel,
    'qr_version' => $qrVersion,
    'qr_logo_space' => $qrLogoSpace,
    'qr_logo_url' => $qrLogoUrl,
]);
echo form_close();
?>
</section>

<section>

<?php
echo "<div class='row mt-5 px-1 d-flex justify-content-around'>";
if($qrCodesBase64 && ! $error) {

    echo "<p>QR codes are in SVG format that you can open in image editors such as " 
        . a("https://inkscape.org", "Inkscape") . " to edit them further.</p>";

    if($qrCodesAsPdf) {
        echo "<div class='col-4'>";
        echo "<a class='btn btn-link' download='qr_codes.pdf' href='$qrCodesAsPdf'>Download All As PDF</a>";
        echo "</div>";
    }
    if($qrCodesAsZip) {
        echo "<div class='col-4'>";
        echo "<a class='btn btn-link' download='qr_codes.zip' href='$qrCodesAsZip'>Download All (zip)</a>";
        echo "</div>";
    }
    echo "</div>";

    echo "<div class='row'>";
    foreach($qrCodesBase64 as $i => $b64QrCode) {
        echo "<div class='col-4'>";
        echo img($b64QrCode, attributes: [
            'width' => '100%',
        ]) . '<br />';

        $filename = "qrcode-{$qrSizeInPx}x{$qrSizeInPx}-$i.svg";
        echo "<a style='float:right' download='$filename' href='$b64QrCode'>Download SVG</a>";
        echo "</div>";
    }
    echo "</div>";
}
if($error) {
    echo "<div class='row text-warning'>" . $error . "</div>";
}

?>
</section>

<?php echo $this->endSection(); ?>
