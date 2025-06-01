<?php

echo $this->extend('default');
echo $this->section('content');

$lines = $lines ?? 'https://tools.maxflow.in';
$qrCodesBase64 = $result ?? [];
$eccLevel = $ecc_level ?? 'M';
$qrSizeInPx = $qr_size_in_px ?? 512;

if(! function_exists('renderQrForm')) {

    /**
     * @param array<string, string> $params
     */
    function renderQrForm(string $lines, array $params = []): string 
    {
        $qrSizeInPx = $params['qr_size_in_px'] ?? '256';

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
        $html[] = formInputBootstrap('qr_size', label: "QR Size (px)", value: $qrSizeInPx, type: 'number');

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
echo '<p>Write one line for each QR code (maximum of 20 lines).
    QR version will be selected automatically.</p>';
echo renderQrForm($lines, params: [
    'qr_size_in_px' => $qrSizeInPx,
    'ecc_level' => $eccLevel,
]);
echo form_close();
?>
</section>

<section>
<?php

if($qrCodesBase64) {
    echo "<p>Your QR codes are ready.</p>";
    echo "<div class='row'>";
    foreach($qrCodesBase64 as $i => $b64QrCode) {
        echo "<div class='col-4'>";
        echo img($b64QrCode, attributes: [
            'width' => '100%',
        ]) . '<br />';

        $filename = "qrcode-{$qrSizeInPx}x{$qrSizeInPx}-$i.png";
        echo "<a style='float:right' download='$filename' href='$b64QrCode'>Download</a>";
        echo "</div>";
    }
    echo "</div>";
}

?>
</section>

<?php echo $this->endSection(); ?>
