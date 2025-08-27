<?php

echo $this->extend('default');
echo $this->section('content');

$lines ??= 'https://tools.maxflow.in
tel:9876543210
mailto:sherpa@maxflow.in
';

// result as pdf file.
$qrCodesAsPdf = $pdf ?? '';
// results as zip file.
$qrCodesAsZip = $zip ?? '';

// base64 encoded QR codes.
$qrCodesBase64 = $result ?? [];

$error ??= null;
$eccLevel = $ecc_level ?? 'H';
$qrSizeInPx = $qr_size_in_px ?? '256';
$qrLogoSpace = $qr_logo_space ?? '10';
$qrLogoUrl = $qr_logo_url ?? 'https://tools.maxflow.in/icon.jpg';
$qrVersion = $qr_version ?? '5';

if (! function_exists('renderQrForm')) {

    /**
     * @param array<string, string> $params
     */
    function renderQrForm(string $lines, array $params = []): string
    {
        $qrSizeInPx = $params['qr_size_in_px'] ?? '256';
        $qrVersion = $params['qr_version'] ?? '5';
        $qrLogoSpace = $params['qr_logo_space'] ?? '10';
        $qrLogoUrl = $params['qr_logo_url'] ?? '10';

        $html[] = '<div>';
        $html[] = "<details class='mt-1 readable'>
            <summary style='float:right'>Help</summary>
            <ul class='help'>
                <li>
                    <strong>QR Version</strong>
                    The more content you include, the larger the QR version you should select.
                </li>
                <li>
                    <strong>ECC (Error Correction Level)</strong> 
                    ECC helps make your QR code resistant to damage. A higher
                    ECC level improves durability. If you're adding a logo, it's
                    recommended to choose ECC level ‘H’ for better reliability.
                </li>
                <li>
                    <strong>Adding a logo</strong>
                    To include a logo in your QR code, provide a URL to the logo
                    image. We currently do not support uploading logo files
                    directly. If we're unable to fetch the logo from the
                    provided link, the logo area will be left blank.
                </li>
            </ul>
        </details>";

        // Row for textarea
        $html[] = "<div class='form-label text-info'>
            <small> Each line in the form below will be converted to a separate QR code. </small>
        </div>";

        $html[] = "<div class='row'>";
        $html[] = "<div class='col-10'>";
        $html[] = form_textarea('lines', $lines, extra: [
            'class' => 'form-control',
            'rows' => '4',
        ]);
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = "<div class='h5 mt-5 title'>QR Options</div>";

        // Row for size.
        $html[] = formInputBootstrap(
            'qr_size_in_px',
            label: 'QR Size For PDF (in px)',
            value: $qrSizeInPx,
            type: 'number'
        );

        // row for version.
        $html[] = formInputBootstrap(
            'qr_version',
            label: 'QR Version',
            value: $qrVersion,
            type: 'number'
        );

        // Row for select.
        $options = [
            'L' => 'L',
            'M' => 'M',
            'Q' => 'Q',
            'H' => 'H',
        ];
        $html[] = formSelectBootstrap(
            'ecc_level',
            label: 'ECC (Error Correction) Level',
            value: $params['ecc_level'] ?? 'M',
            options: $options,
        );

        // logo space.
        $html[] = formInputBootstrap(
            'qr_logo_space',
            label: 'Logo Space (typically between 10% and 25%)',
            value: $qrLogoSpace,
            type: 'number'
        );

        // logo url.
        $html[] = formInputBootstrap(
            'qr_logo_url',
            label: 'Logo URL (image url)',
            value: $qrLogoUrl,
            type: 'text',
        );

        // Row for the submit button.
        $html[] = "<div class='row d-flex justify-content-end mt-3'>";
        $html[] = "<div class='col-6'>";
        $html[] = form_submit('submit', 'Generate', extra: [
            'class' => 'btn btn-primary col-6',
        ]);
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(' ', $html);
    }
}

?>

<section>
<div class="h3 section-title">QR Code Generator</div>

<?php echo form_open('/tool/qrcodes/generate');
echo '<p class="readable">
    This tool can generate upto 20 QR codes in one go. To insert your logo, add
    its image URL.  Download them as ZIP or PDF file.

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
    <div class='result'>
<?php
if ($qrCodesBase64 && ! $error) {

    echo '<div class="row">';
    if ($qrCodesAsPdf) {
        echo "<div class='col-4'>";
        echo "<a class='btn btn-link' download='qr_codes.pdf' href='$qrCodesAsPdf'>Download All As PDF</a>";
        echo '</div>';
    }
    if ($qrCodesAsZip) {
        echo "<div class='col-4'>";
        echo "<a class='btn btn-link' download='qr_codes.zip' href='$qrCodesAsZip'>Download All (zip)</a>";
        echo '</div>';
    }
    echo '</div>';

    echo '<section>';
    echo '<p>You can also download individual QR code. These are in SVG format that 
        you can edit in image editors such as '
        .a('https://inkscape.org', 'Inkscape').'.</p>';

    echo "<div class='row d-flex justify-content-between'>";
    foreach ($qrCodesBase64 as $i => $b64QrCode) {
        echo "<div class='col-4'>";
        echo img($b64QrCode, attributes: [
            'width' => '100%',
        ]).'<br />';

        $filename = "qrcode-{$qrSizeInPx}x{$qrSizeInPx}-$i.svg";
        echo "<a class='btn btn-link text-align-center' download='$filename' href='$b64QrCode'>Download SVG</a>";
        echo '</div>';
    }
    echo '</div>';
    echo '</section>';
}

if ($error) {
    echo "<div class='row text-warning'>".$error.'</div>';
}
?>
    </div>
</section>

<section class="mt-5 px-5">
    <span class='h6'>Credits:</span> This tool uses excellent 
    <a href="https://github.com/chillerlan/php-qrcode">chillerlan/php-qrcode</a> library.
</section>

<?php echo $this->endSection(); ?>
