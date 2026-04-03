<?php

echo $this->extend('default');
echo $this->section('content');

$lines ??= 'https://tools.dilawars.me
tel:9876543210
mailto:hi@dilawars.me
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
$qrLogoUrl = $qr_logo_url ?? 'https://tools.dilawars.me/icon.jpg';
$qrVersion = $qr_version ?? '5';

$helpText = "
<details>
    <summary class=’text-info’>Help</summary>
    <ul class=’readable text-info’>
        <li>
            <strong>QR Version</strong>
            The more content you include, the larger the QR version you should select.
        </li>
        <li>
            <strong>ECC (Error Correction Level)</strong>
            ECC helps make your QR code resistant to damage. A higher
            ECC level improves durability. If you’re adding a logo, it’s
            recommended to choose ECC level ‘H’ for better reliability.
        </li>
        <li>
            <strong>Adding a logo</strong>
            To include a logo in your QR code, provide a URL to the logo
            image. We currently do not support uploading logo files
            directly. If we’re unable to fetch the logo from the
            provided link, the logo area will be left blank.
        </li>
    </ul>
</details>
";

if (! function_exists('renderQrForm')) {
    /**
     * @param array<string, string> $params
     */
    function renderQrForm(string $lines, string $helpText = '', array $params = []): string
    {
        $qrSizeInPx = $params['qr_size_in_px'] ?? '256';
        $qrVersion = $params['qr_version'] ?? '5';
        $qrLogoSpace = $params['qr_logo_space'] ?? '10';
        $qrLogoUrl = $params['qr_logo_url'] ?? '10';

        $html = [];

        if ($helpText) {
            $html[] = '<div class="mb-3">'.$helpText.'</div>';
        }

        $html[] = "<div class='mb-3'>";
        $html[] = "<label class='form-label fw-semibold' for='qr-lines'>Content <small class='fw-normal' style='color:var(--text-secondary)'>— one QR code per line</small></label>";
        $html[] = form_textarea('lines', $lines, extra: [
            'id'    => 'qr-lines',
            'class' => 'form-control',
            'rows'  => '4',
        ]);
        $html[] = '</div>';

        $html[] = "<p class='fw-semibold mb-2' style='border-top:1px solid var(--border-light); padding-top:1rem;'>QR Options</p>";

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

        $html[] = "<div class='mt-3'>";
        $html[] = form_submit('submit', 'Generate', extra: [
            'class' => 'btn btn-primary',
        ]);
        $html[] = '</div>';

        return implode(' ', $html);
    }
}

?>

<section>
<h1 class="section-title">Bulk QR Code Generator</h1>
<p class="page-lead">
    Generate up to 20 QR codes at once. Download them all as a single PDF or a ZIP of SVG files.
    Want a live preview and embeddable URL? <a href="/tool/qrcodes">Use the single QR builder.</a>
</p>

<div class="form-section">
<?php echo form_open('/tool/qrcodes/generate');

echo renderQrForm($lines, helpText: $helpText, params: [
    'qr_size_in_px' => $qrSizeInPx,
    'ecc_level' => $eccLevel,
    'qr_version' => $qrVersion,
    'qr_logo_space' => $qrLogoSpace,
    'qr_logo_url' => $qrLogoUrl,
]);
echo form_close();
?>
</div>
</section>

<?php if ($qrCodesBase64 && ! $error): ?>
<section>
<div class="result">
    <div class="row mb-2">
        <?php if ($qrCodesAsPdf): ?>
        <div class="col-auto">
            <?php echo sprintf("<a class='btn btn-primary btn-sm' download='qr_codes.pdf' href='%s'>Download all as PDF</a>", $qrCodesAsPdf); ?>
        </div>
        <?php endif; ?>
        <?php if ($qrCodesAsZip): ?>
        <div class="col-auto">
            <?php echo sprintf("<a class='btn btn-outline-secondary btn-sm' download='qr_codes.zip' href='%s'>Download all as ZIP</a>", $qrCodesAsZip); ?>
        </div>
        <?php endif; ?>
    </div>

    <p class="page-lead mb-2">SVG files — editable in <?php echo a('https://inkscape.org', 'Inkscape'); ?> or any vector editor.</p>

    <div class="row g-3">
        <?php foreach ($qrCodesBase64 as $i => $b64QrCode): ?>
        <div class="col-4 col-sm-3 text-center">
            <?php echo img($b64QrCode, attributes: ['width' => '100%']); ?>
            <?php $filename = sprintf('qrcode-%sx%s-%s.svg', $qrSizeInPx, $qrSizeInPx, $i); ?>
            <a class="btn btn-link btn-sm" download="<?php echo $filename; ?>" href="<?php echo $b64QrCode; ?>">SVG</a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</section>
<?php endif; ?>

<?php if ($error): ?>
<section>
    <div class="result">
        <p class="text-warning mb-0"><?php echo $error; ?></p>
    </div>
</section>
<?php endif; ?>

<section class="mt-4">
    <small style="color:var(--text-secondary);">
        Uses the <a href="https://github.com/chillerlan/php-qrcode">chillerlan/php-qrcode</a> library.
    </small>
</section>

<?php echo $this->endSection(); ?>
