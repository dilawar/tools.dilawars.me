<?php

use App\Data\ToolActionName;

echo $this->extend('default');
echo $this->section('content');

// To format.
$toFormat = $to ?? 'jpeg';

// From format.
$fromFormat = 'pdf';

$imagesArtifacts = $image_artifacts ?? [];

/**
 * @var array<string>
 */
$supportedFormats = supportedImageFormats();

if (! function_exists('renderPdfCompressForm')) {
    function renderPdfCompressForm(): string
    {
        $html = [];

        $html[] = "<div class='mb-3'>";
        $html[] = "<label class='form-label fw-semibold' for='pdf-upload'>PDF file</label>";
        $html[] = form_input('image', type: 'file', extra: [
            'id'     => 'pdf-upload',
            'class'  => 'form-control',
            'accept' => '.pdf',
        ]);
        $html[] = '</div>';

        $html[] = form_submit('submit', 'Compress', extra: [
            'class' => 'btn btn-primary',
        ]);

        return implode(' ', $html);
    }
}

?>

<section>

<h1 class="section-title">Compress PDF</h1>
<p class="page-lead">Shrink large PDF files to a fraction of their original size.</p>

<div class="form-section">
<?php
echo form_open_multipart('/tool/pdf/'.ToolActionName::PdfCompress->value);
echo renderPdfCompressForm();
echo '</form>';
?>
</div>
</section>

<section>
    <?php echo view_cell('DownloadFileCell', [
        'images' => $imagesArtifacts,
    ]); ?>
</section>

<?php echo $this->endSection(); ?>
