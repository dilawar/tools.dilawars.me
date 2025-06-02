<?php

echo $this->extend('default');
echo $this->section('content');

$lines = $lines ?? 'https://tools.maxflow.in';

// result as pdf file.
$qrCodesAsPdf = $pdf ?? '';

// base64 encoded QR codes.
$qrCodesBase64 = $result ?? [];

$error = $error ?? null;
$eccLevel = $ecc_level ?? 'H';
$qrSizeInPx = $qr_size_in_px ?? '256';
$qrLogoSpace = $qr_logo_space ?? '10';
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

        $html[] = "<div>";
        // Row for textarea
        $html[] = "<div class='row'>";
        $html[] = "<div class='col-10'>";
        $html[] = form_textarea("lines", $lines, extra: [
            'class' => 'form-control',
            'rows' => "3",
        ]);
        $html[] = "</div>";
        $html[] = "</div>";

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
            label: "Error Correction Code (ECC) Level",
            value: $params['ecc_level'] ?? 'M',
            options: $options,
        );

        // logo space.
        $html[] = formInputBootstrap('qr_logo_space', label: "Logo Space (typically between 10 and 25)", value: $qrLogoSpace, type: 'number');

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

<h3 class="section-title">Generate Qr Codes</h3>

<section>
<?php echo form_open('/tool/qrcodes/generate');
echo '<p>Write one line for each QR code (maximum of 20 lines).</p>';
echo renderQrForm($lines, params: [
    'qr_size_in_px' => $qrSizeInPx,
    'ecc_level' => $eccLevel,
    'qr_version' => $qrVersion,
    'qr_logo_space' => $qrLogoSpace,
]);
echo form_close();
?>
</section>

<section>
<?php

if($qrCodesBase64 && ! $error) {
    echo "<p>Download your QR codes. These are in SVG formats that you can open in image editors such as " 
        . link_tag("https://inkscape.org", "Inkscape") 
        . " and edit them further.</p>";
    echo "<div class='row'>";
    foreach($qrCodesBase64 as $i => $b64QrCode) {
        echo "<div class='col-4'>";
        echo img($b64QrCode, attributes: [
            'width' => '100%',
        ]) . '<br />';

        $filename = "qrcode-{$qrSizeInPx}x{$qrSizeInPx}-$i.svg";
        echo "<a style='float:right' download='$filename' href='$b64QrCode'>Download</a>";
        echo "</div>";
    }
    echo "</div>";
}
if($error) {
    echo "<div class='row text-warning'>" . $error . "</div>";
}

if($qrCodesAsPdf) {
    echo "<a download='qr_codes.pdf' href='$qrCodesAsPdf'>Download All QR Codes As PDF</a>";
}

?>
</section>

<?php echo $this->endSection(); ?>
