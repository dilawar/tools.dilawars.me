<?php echo $this->extend('default'); ?>
<?php echo $this->section('content'); ?>

<?php

if (! function_exists('renderToolCard')) {
    /**
     * @param array{href: string, text: string} $link
     */
    function renderToolCard(string $body, array $link): string
    {
        assert(is_string($link['href']));
        $html = [];
        $html[] = "<div class='readable'>";
        $html[] = "<a class='tool-card' href='".$link['href']."'>"
            ."<span class='tool-title'>".$link['text'].'</span>'
            ."<p class='tool-body'>".htmlspecialchars($body).'</p>'
            .'</a>';
        $html[] = '</div>';

        return implode(' ', $html);
    }
}

?>

<section>
    <h1 class="section-title">Free Online Tools</h1>
    <p class="page-lead">
        Simple, fast, and private. Everything runs in your browser or on this server —
        no account required, no files stored longer than needed.
    </p>

    <?php echo renderToolCard(
        body: 'Generate a single embeddable QR code with a live preview, or produce up to 20 at once and download them as a PDF or ZIP. Supports custom logos and error-correction levels.',
        link: ['href' => '/tool/qrcodes', 'text' => 'QR Code Generator'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Reduce image file size without a noticeable drop in quality. Accepts JPEG, PNG, HEIC, WebP, and more — outputs a compressed JPEG ready for web, email, or storage.',
        link: ['href' => '/tool/compress', 'text' => 'Image Compressor'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Convert images between JPG, PNG, HEIC, BMP, GIF, WebP, and over 100 other formats. Upload any format, pick your target, and download instantly.',
        link: ['href' => '/tool/convert', 'text' => 'Image Converter'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Extract every page of a PDF as a high-quality JPEG image. Useful for sharing individual pages, embedding in documents, or archiving scanned files.',
        link: ['href' => '/tool/pdf/to_jpeg', 'text' => 'PDF to JPG'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Shrink large PDF files to a fraction of their original size — making them faster to email, upload, or store — without sacrificing readability.',
        link: ['href' => '/tool/pdf/compress', 'text' => 'Compress PDF'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Pull text out of scanned PDFs and images using optical character recognition. Processing happens locally in your browser — nothing is uploaded to a server.',
        link: ['href' => '/tool/ocr/extract', 'text' => 'OCR — Extract Text'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Draw a running, cycling, or hiking route on an interactive map and download it as a GPX file for your GPS device or fitness app.',
        link: ['href' => '/tool/geo/map_route', 'text' => 'Map My Route'],
    ); ?>

    <?php echo renderToolCard(
        body: 'Get an email the moment a recent LWN.net article becomes publicly available, so you can read it without a subscription.',
        link: ['href' => '/tool/subscription/lwn', 'text' => 'LWN Article Alerts'],
    ); ?>

</section>

<?php echo $this->endSection(); ?>
