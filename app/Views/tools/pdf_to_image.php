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

if (! function_exists('renderUploadFormInner')) {
    /**
     * @param array<string> $formats
     */
    function renderUploadFormInner(string $toFormat, array $formats): string
    {
        $imageFormats = [];
        foreach ($formats as $format) {
            $format = strtolower($format);
            $imageFormats[$format] = $format;
        }

        $html = [];

        // File picker — full width
        $html[] = "<div class='mb-3'>";
        $html[] = "<label class='form-label fw-semibold' for='pdf-upload'>PDF file</label>";
        $html[] = form_input('image', type: 'file', extra: [
            'id'     => 'pdf-upload',
            'class'  => 'form-control',
            'accept' => '.pdf',
        ]);
        $html[] = '</div>';

        // Format selector + submit — same row, aligned to bottom
        $html[] = "<div class='row g-3 align-items-end'>";

        $html[] = "<div class='col-sm-4'>";
        $html[] = "<label class='form-label fw-semibold' for='".SELECTIZE_ID_PREFIX."_to_format'>Output format</label>";
        $html[] = form_dropdown(
            'to_format',
            options: $imageFormats,
            selected: $toFormat,
            extra: [
                'id'    => SELECTIZE_ID_PREFIX.'_to_format',
                'class' => 'form-select',
            ],
        );
        $html[] = '</div>';

        $html[] = "<div class='col-auto'>";
        $html[] = form_submit('submit', 'Convert', extra: [
            'class' => 'btn btn-primary',
        ]);
        $html[] = '</div>';

        $html[] = '</div>'; // row

        return implode(' ', $html);
    }
}

?>

<section>

<h1 class="section-title">PDF to Image</h1>
<p class="page-lead">Convert every page of a PDF to an image. Defaults to JPEG — change the output format below if needed.</p>

<div class="form-section">
<?php
$hidden = [
    'from' => $fromFormat,
    'to' => $toFormat,
];
echo form_open_multipart('/tool/pdf/'.ToolActionName::PdfConvertToJpeg->value, hidden: $hidden);
echo renderUploadFormInner($toFormat, $supportedFormats);
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
